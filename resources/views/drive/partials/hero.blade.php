<section class="glass-panel p-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Drive path</p>
            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-100 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-200">
                    My Drive
                </a>
                @foreach ($breadcrumbs as $crumb)
                    <span>/</span>
                    <a
                        href="{{ $dashboardUrl($crumb->id) }}"
                        class="rounded-full px-3 py-1.5 font-medium {{ $loop->last ? 'bg-sky-50 text-sky-700' : 'bg-slate-100 text-slate-700 transition hover:bg-slate-200' }}"
                    >
                        {{ $crumb->name }}
                    </a>
                @endforeach
            </div>

            <h2 class="mt-5 text-3xl font-semibold text-slate-900">
                {{ $currentFolder?->name ?? 'My Drive' }}
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                @if ($search !== '')
                    Menampilkan hasil pencarian untuk <span class="font-semibold text-slate-700">"{{ $search }}"</span> di folder aktif.
                @else
                    {{ $folders->count() }} folder dan {{ $files->count() }} file tersedia di tampilan ini.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            @if ($currentFolder)
                <a href="{{ $dashboardUrl($currentFolder->parent_id) }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                    </svg>
                    Up one level
                </a>
            @endif

            <button type="button" class="btn-secondary" @click="createFolderOpen = true">
                New folder
            </button>
            <button type="button" class="btn-primary" @click="uploadOpen = true">
                Upload files
            </button>
        </div>
    </div>
</section>
