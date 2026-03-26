<?php

namespace App\Http\Controllers;

use App\Models\DriveFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriveFileController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'folder_id' => ['nullable', 'integer'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file'],
        ]);

        $folder = $validated['folder_id'] ?? null
            ? $request->user()->driveFolders()->findOrFail($validated['folder_id'])
            : null;

        $uploadedFiles = $request->file('files', []);

        foreach ($uploadedFiles as $uploadedFile) {
            $originalName = trim($uploadedFile->getClientOriginalName());
            $fileName = $this->availableFileName($request, $folder?->id, $originalName);
            $storedName = (string) Str::ulid();
            $extension = $uploadedFile->getClientOriginalExtension();
            $storageName = $extension !== '' ? "{$storedName}.{$extension}" : $storedName;
            $path = $uploadedFile->storeAs(
                'drive/'.$request->user()->id.'/'.now()->format('Y/m'),
                $storageName,
                'local',
            );

            $request->user()->driveFiles()->create([
                'drive_folder_id' => $folder?->id,
                'name' => $fileName,
                'disk' => 'local',
                'path' => $path,
                'mime_type' => $uploadedFile->getClientMimeType(),
                'extension' => $extension !== '' ? strtolower($extension) : null,
                'size' => $uploadedFile->getSize(),
            ]);
        }

        $message = count($uploadedFiles) > 1 ? 'Files uploaded.' : 'File uploaded.';

        return redirect()
            ->route('dashboard', $this->folderContext($folder?->id))
            ->with('status', $message);
    }

    public function update(Request $request, DriveFile $file): RedirectResponse
    {
        abort_unless($file->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160', 'not_regex:/[\/\\\\]/'],
        ], [
            'name.not_regex' => 'File name cannot contain slashes.',
        ]);

        $name = trim($validated['name']);

        if ($name === '') {
            return back()->withErrors(['name' => 'File name is required.']);
        }

        if ($this->fileNameExists($request, $file->drive_folder_id, $name, $file->id)) {
            return back()->withErrors(['name' => 'A file with that name already exists here.']);
        }

        $file->update(['name' => $name]);

        return redirect()
            ->route('dashboard', $this->folderContext($file->drive_folder_id))
            ->with('status', 'File renamed.');
    }

    public function destroy(Request $request, DriveFile $file): RedirectResponse
    {
        abort_unless($file->user_id === $request->user()->id, 404);

        Storage::disk($file->disk)->delete($file->path);

        $folderId = $file->drive_folder_id;

        $file->delete();

        return redirect()
            ->route('dashboard', $this->folderContext($folderId))
            ->with('status', 'File deleted.');
    }

    public function download(Request $request, DriveFile $file): StreamedResponse
    {
        abort_unless($file->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    private function availableFileName(
        Request $request,
        ?int $folderId,
        string $requestedName,
        ?int $ignoreFileId = null,
    ): string {
        $candidate = $requestedName;
        $extension = pathinfo($requestedName, PATHINFO_EXTENSION);
        $baseName = pathinfo($requestedName, PATHINFO_FILENAME);

        if ($baseName === '' && $extension === '') {
            $baseName = $requestedName;
        }

        $counter = 2;

        while ($this->fileNameExists($request, $folderId, $candidate, $ignoreFileId)) {
            $candidate = $baseName." ({$counter})".($extension !== '' ? ".{$extension}" : '');
            $counter++;
        }

        return $candidate;
    }

    private function fileNameExists(
        Request $request,
        ?int $folderId,
        string $name,
        ?int $ignoreFileId = null,
    ): bool {
        return $request->user()->driveFiles()
            ->where('drive_folder_id', $folderId)
            ->when($ignoreFileId, fn ($query) => $query->whereKeyNot($ignoreFileId))
            ->where('name', $name)
            ->exists();
    }

    private function folderContext(?int $folderId): array
    {
        return $folderId ? ['folder' => $folderId] : [];
    }
}
