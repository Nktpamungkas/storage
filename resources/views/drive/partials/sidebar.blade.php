<aside class="space-y-5">
    <section class="glass-panel p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Personal Cloud</p>
        <h1 class="mt-3 text-2xl font-semibold text-slate-900">
            {{ $currentFolder?->name ?? 'My Drive' }}
        </h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Workspace pribadi buat transit file, folder kerja, dan arsip sementara langsung dari VPS kamu.
        </p>

        <div class="mt-5 flex flex-wrap gap-3">
            <button type="button" class="btn-primary" @click="createFolderOpen = true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                New folder
            </button>
            <button type="button" class="btn-secondary" @click="uploadOpen = true">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0-4 4m4-4 4 4M4 16.5V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1.5" />
                </svg>
                Upload files
            </button>
        </div>
    </section>

    <section class="surface-panel p-5">
        <div class="grid gap-3">
            <div class="stat-tile">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Free VPS disk</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $formatBytes($stats['disk_free']) }}</p>
                <p class="mt-2 text-xs text-slate-500">Sisa ruang partisi server tempat file storage ini disimpan.</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="stat-tile">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">App files</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ $formatBytes($stats['storage_used']) }}</p>
                </div>
                <div class="stat-tile">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Total disk</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ $formatBytes($stats['disk_total']) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="stat-tile">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Folders</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ $stats['folder_count'] }}</p>
                </div>
                <div class="stat-tile">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Files</p>
                    <p class="mt-2 text-xl font-semibold text-slate-900">{{ $stats['file_count'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="surface-panel p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Folders</h2>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">
                Root
            </a>
        </div>

        <div class="mt-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ $currentFolder === null ? 'sidebar-link-active' : '' }}">
                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h3.171a2 2 0 0 1 1.414.586l1.329 1.328a2 2 0 0 0 1.414.586H18.5A2.5 2.5 0 0 1 21 10v8.5A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5v-11Z" />
                    </svg>
                </span>
                My Drive
            </a>

            @forelse ($rootFolders as $folder)
                <a href="{{ $dashboardUrl($folder->id) }}" class="sidebar-link {{ $currentRootId === $folder->id ? 'sidebar-link-active' : '' }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h3.171a2 2 0 0 1 1.414.586l1.329 1.328a2 2 0 0 0 1.414.586H18.5A2.5 2.5 0 0 1 21 10v8.5A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5v-11Z" />
                        </svg>
                    </span>
                    <span class="truncate">{{ $folder->name }}</span>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">
                    Belum ada folder root.
                </div>
            @endforelse
        </div>
    </section>

    <section class="surface-panel p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Recent uploads</h2>
            <span class="badge-muted">{{ $recentFiles->count() }}</span>
        </div>

        <div class="mt-4 space-y-3">
            @forelse ($recentFiles as $recentFile)
                <div class="rounded-2xl border border-slate-200/70 bg-slate-50/80 px-4 py-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $recentFile->name }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500">
                                {{ $recentFile->folder?->name ?? 'Root' }} • {{ $recentFile->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('drive.files.download', $recentFile) }}" class="badge-muted">
                            Open
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500">
                    Upload pertama kamu akan muncul di sini.
                </div>
            @endforelse
        </div>
    </section>
</aside>
