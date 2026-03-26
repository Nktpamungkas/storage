<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DriveController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $folderId = $request->filled('folder') ? (int) $request->input('folder') : null;
        $search = trim((string) $request->input('search', ''));

        $currentFolder = null;

        if ($folderId !== null) {
            $currentFolder = $user->driveFolders()->with('parent')->findOrFail($folderId);
        }

        $folders = $user->driveFolders()
            ->where('parent_id', $currentFolder?->id)
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->withCount(['children', 'files'])
            ->orderBy('name')
            ->get();

        $files = $user->driveFiles()
            ->where('drive_folder_id', $currentFolder?->id)
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        return view('drive.index', [
            'breadcrumbs' => $currentFolder?->breadcrumbs() ?? collect(),
            'currentFolder' => $currentFolder,
            'files' => $files,
            'folders' => $folders,
            'recentFiles' => $user->driveFiles()->with('folder')->latest()->limit(5)->get(),
            'rootFolders' => $user->driveFolders()->whereNull('parent_id')->orderBy('name')->limit(8)->get(),
            'search' => $search,
            'stats' => [
                'file_count' => $user->driveFiles()->count(),
                'folder_count' => $user->driveFolders()->count(),
                'storage_used' => (int) $user->driveFiles()->sum('size'),
            ],
        ]);
    }
}
