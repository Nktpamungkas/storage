<section class="surface-panel overflow-hidden">
    <div class="flex flex-col gap-4 border-b border-slate-200/70 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-slate-900">
                {{ $fileView === 'largest' ? 'Largest Files' : 'Files' }}
            </h3>
            <p class="mt-1 text-sm text-slate-500">
                {{ $fileView === 'largest'
                    ? 'Mode cleanup untuk melihat file paling besar di seluruh akun kamu.'
                    : 'Semua file pada folder aktif, siap diunduh atau dirapikan.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a
                href="{{ $dashboardUrl($currentFolder?->id, ['search' => $search]) }}"
                class="btn-secondary {{ $fileView === 'folder' ? '!border-sky-100 !bg-sky-50 !text-sky-700' : '' }}"
            >
                Folder files
            </a>
            <a
                href="{{ $dashboardUrl($currentFolder?->id, ['file_view' => 'largest', 'search' => $search]) }}"
                class="btn-secondary {{ $fileView === 'largest' ? '!border-sky-100 !bg-sky-50 !text-sky-700' : '' }}"
            >
                File terbesar
            </a>
            <span class="badge-muted">{{ $files->count() }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70 text-sm">
            <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Size</th>
                    <th class="px-6 py-4">Updated</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200/60 bg-white">
                @forelse ($files as $file)
                    <tr class="transition hover:bg-slate-50/80">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-xs font-bold uppercase tracking-wide text-sky-700">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($file->extension ?: 'file', 4, '')) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $file->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $fileView === 'largest' ? ($file->folder?->name ?? 'Root') : ($currentFolder?->name ?? 'Root') }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $file->mime_type ?: 'Unknown file' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $formatBytes($file->size) }}
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $file->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('drive.files.download', $file) }}" class="btn-secondary !px-3 !py-2">
                                    Download
                                </a>
                                <button
                                    type="button"
                                    class="btn-secondary !px-3 !py-2"
                                    @click="renameFile = { id: {{ $file->id }}, name: @js($file->name) }"
                                >
                                    Rename
                                </button>
                                <form method="POST" action="{{ route('drive.files.destroy', $file) }}" onsubmit="return confirm('Delete this file?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger !px-3 !py-2">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center text-sm text-slate-500">
                            {{ $fileView === 'largest'
                                ? 'Belum ada file yang bisa ditampilkan untuk mode largest files.'
                                : 'Belum ada file di folder ini. Kamu bisa upload beberapa file sekaligus dari tombol di atas.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
