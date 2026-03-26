<?php

namespace App\Http\Controllers;

use App\Models\DriveFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DriveFolderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'not_regex:/[\/\\\\]/'],
            'parent_id' => ['nullable', 'integer'],
        ], [
            'name.not_regex' => 'Folder name cannot contain slashes.',
        ]);

        $parent = $validated['parent_id'] ?? null
            ? $request->user()->driveFolders()->findOrFail($validated['parent_id'])
            : null;

        $name = trim($validated['name']);

        if ($name === '') {
            return back()->withErrors(['name' => 'Folder name is required.'])->withInput();
        }

        if ($this->folderNameExists($request, $parent?->id, $name)) {
            return back()
                ->withErrors(['name' => 'A folder with that name already exists here.'])
                ->withInput();
        }

        $request->user()->driveFolders()->create([
            'name' => $name,
            'parent_id' => $parent?->id,
        ]);

        return redirect()
            ->route('dashboard', $this->folderContext($parent?->id))
            ->with('status', 'Folder created.');
    }

    public function update(Request $request, DriveFolder $folder): RedirectResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'not_regex:/[\/\\\\]/'],
        ], [
            'name.not_regex' => 'Folder name cannot contain slashes.',
        ]);

        $name = trim($validated['name']);

        if ($name === '') {
            return back()->withErrors(['name' => 'Folder name is required.']);
        }

        if ($this->folderNameExists($request, $folder->parent_id, $name, $folder->id)) {
            return back()->withErrors(['name' => 'A folder with that name already exists here.']);
        }

        $folder->update(['name' => $name]);

        return redirect()
            ->route('dashboard', $this->folderContext($folder->parent_id))
            ->with('status', 'Folder renamed.');
    }

    public function destroy(Request $request, DriveFolder $folder): RedirectResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 404);

        $folderIds = $this->descendantFolderIds(
            $request->user()->driveFolders()->get(['id', 'parent_id']),
            $folder->id,
        );

        $request->user()->driveFiles()
            ->whereIn('drive_folder_id', $folderIds)
            ->get(['disk', 'path'])
            ->each(fn ($file) => Storage::disk($file->disk)->delete($file->path));

        $parentId = $folder->parent_id;

        $foldersById = $request->user()->driveFolders()
            ->whereIn('id', $folderIds)
            ->get()
            ->keyBy('id');

        foreach (array_reverse($folderIds) as $folderId) {
            $foldersById->get($folderId)?->delete();
        }

        return redirect()
            ->route('dashboard', $this->folderContext($parentId))
            ->with('status', 'Folder deleted.');
    }

    private function descendantFolderIds(Collection $folders, int $rootId): array
    {
        $childrenByParent = $folders->groupBy('parent_id');
        $ids = [];
        $stack = [$rootId];

        while ($stack !== []) {
            $folderId = array_pop($stack);
            $ids[] = $folderId;

            foreach ($childrenByParent->get($folderId, collect()) as $child) {
                $stack[] = $child->id;
            }
        }

        return $ids;
    }

    private function folderContext(?int $folderId): array
    {
        return $folderId ? ['folder' => $folderId] : [];
    }

    private function folderNameExists(
        Request $request,
        ?int $parentId,
        string $name,
        ?int $ignoreFolderId = null,
    ): bool {
        return $request->user()->driveFolders()
            ->where('parent_id', $parentId)
            ->when($ignoreFolderId, fn ($query) => $query->whereKeyNot($ignoreFolderId))
            ->where('name', $name)
            ->exists();
    }
}
