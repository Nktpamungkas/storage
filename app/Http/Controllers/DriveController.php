<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DriveController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $folderId = $request->filled('folder') ? (int) $request->input('folder') : null;
        $fileView = $request->input('file_view') === 'largest' ? 'largest' : 'folder';
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

        $filesQuery = $user->driveFiles()
            ->with('folder')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"));

        if ($fileView === 'largest') {
            $filesQuery
                ->orderByDesc('size')
                ->orderBy('name');
        } else {
            $filesQuery
                ->where('drive_folder_id', $currentFolder?->id)
                ->orderBy('name');
        }

        $files = $filesQuery->get();

        $diskRoot = Storage::disk('local')->path('');
        $diskFree = @disk_free_space($diskRoot);
        $diskTotal = @disk_total_space($diskRoot);
        $diskFree = $diskFree !== false ? (int) $diskFree : null;
        $diskTotal = $diskTotal !== false ? (int) $diskTotal : null;
        $diskUsed = $diskTotal !== null && $diskFree !== null ? max($diskTotal - $diskFree, 0) : null;
        $diskUsagePercent = $diskTotal ? round(($diskUsed / $diskTotal) * 100, 1) : null;
        $diskFreePercent = $diskTotal ? round(($diskFree / $diskTotal) * 100, 1) : null;

        $storageState = 'normal';

        if ($diskFree !== null && $diskFreePercent !== null) {
            if ($diskFreePercent <= 10 || $diskFree <= 5 * 1024 * 1024 * 1024) {
                $storageState = 'critical';
            } elseif ($diskFreePercent <= 20 || $diskFree <= 10 * 1024 * 1024 * 1024) {
                $storageState = 'warning';
            }
        }

        return view('drive.index', [
            'breadcrumbs' => $currentFolder?->breadcrumbs() ?? collect(),
            'currentFolder' => $currentFolder,
            'files' => $files,
            'fileView' => $fileView,
            'folders' => $folders,
            'recentFiles' => $user->driveFiles()->with('folder')->latest()->limit(5)->get(),
            'rootFolders' => $user->driveFolders()->whereNull('parent_id')->orderBy('name')->limit(8)->get(),
            'search' => $search,
            'stats' => [
                'file_count' => $user->driveFiles()->count(),
                'folder_count' => $user->driveFolders()->count(),
                'storage_used' => (int) $user->driveFiles()->sum('size'),
                'disk_free' => $diskFree,
                'disk_total' => $diskTotal,
                'disk_used' => $diskUsed,
                'disk_usage_percent' => $diskUsagePercent,
                'disk_free_percent' => $diskFreePercent,
                'storage_state' => $storageState,
            ],
        ]);
    }
}
