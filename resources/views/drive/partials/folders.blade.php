<section class="surface-panel p-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">Folders</h3>
            <p class="mt-1 text-sm text-slate-500">Kelola struktur folder dengan cepat dari area kerja utama.</p>
        </div>
        <span class="badge-muted">{{ $folders->count() }}</span>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($folders as $folder)
            <article class="rounded-3xl border border-slate-200/70 bg-slate-50/80 p-5 transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-lg hover:shadow-sky-100/40">
                <div class="flex items-start justify-between gap-4">
                    <a href="{{ $dashboardUrl($folder->id) }}" class="flex min-w-0 items-center gap-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-3xl bg-amber-100 text-amber-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h3.171a2 2 0 0 1 1.414.586l1.329 1.328a2 2 0 0 0 1.414.586H18.5A2.5 2.5 0 0 1 21 10v8.5A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5v-11Z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h4 class="truncate text-base font-semibold text-slate-900">{{ $folder->name }}</h4>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $folder->children_count }} folder • {{ $folder->files_count }} file
                            </p>
                        </div>
                    </a>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ $dashboardUrl($folder->id) }}" class="btn-secondary !px-3 !py-2">
                        Open
                    </a>
                    <button
                        type="button"
                        class="btn-secondary !px-3 !py-2"
                        @click="renameFolder = { id: {{ $folder->id }}, name: @js($folder->name) }"
                    >
                        Rename
                    </button>
                    <form method="POST" action="{{ route('drive.folders.destroy', $folder) }}" onsubmit="return confirm('Delete this folder and everything inside it?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger !px-3 !py-2">
                            Delete
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-3xl border border-dashed border-slate-200 px-6 py-10 text-center text-sm text-slate-500">
                Folder kosong. Mulai dengan bikin folder baru untuk rapikan file kamu.
            </div>
        @endforelse
    </div>
</section>
