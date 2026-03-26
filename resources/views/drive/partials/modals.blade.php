<div x-cloak x-show="createFolderOpen" class="modal-overlay">
    <div x-on:click.outside="createFolderOpen = false" class="modal-card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">Create folder</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">Folder baru</h3>
            </div>
            <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="createFolderOpen = false">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('drive.folders.store') }}" class="mt-6 space-y-4">
            @csrf
            @if ($currentFolder)
                <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
            @endif

            <div>
                <label for="create-folder-name" class="mb-2 block text-sm font-semibold text-slate-700">Folder name</label>
                <input id="create-folder-name" type="text" name="name" class="input-field" placeholder="Project Assets" required>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="createFolderOpen = false">Cancel</button>
                <button type="submit" class="btn-primary">Create folder</button>
            </div>
        </form>
    </div>
</div>

<div x-cloak x-show="uploadOpen" class="modal-overlay">
    <div x-on:click.outside="uploadOpen = false" class="modal-card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">Upload files</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">Tambahkan file</h3>
            </div>
            <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="uploadOpen = false">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('drive.files.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @if ($currentFolder)
                <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
            @endif

            <div>
                <label for="drive-files" class="mb-2 block text-sm font-semibold text-slate-700">Choose one or more files</label>
                <input
                    id="drive-files"
                    type="file"
                    name="files[]"
                    multiple
                    required
                    class="input-field px-3 py-2 file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                >
                <p class="mt-2 text-xs text-slate-500">
                    File akan disimpan secara private di server dan baru bisa diakses setelah login.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="uploadOpen = false">Cancel</button>
                <button type="submit" class="btn-primary">Start upload</button>
            </div>
        </form>
    </div>
</div>

<div x-cloak x-show="renameFolder" class="modal-overlay">
    <div x-on:click.outside="renameFolder = null" class="modal-card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">Rename folder</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">Ubah nama folder</h3>
            </div>
            <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="renameFolder = null">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" :action="folderUpdateTemplate.replace('__FOLDER__', renameFolder?.id ?? '')" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="rename-folder-name" class="mb-2 block text-sm font-semibold text-slate-700">Folder name</label>
                <input id="rename-folder-name" type="text" name="name" x-model="renameFolder.name" class="input-field" required>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="renameFolder = null">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<div x-cloak x-show="renameFile" class="modal-overlay">
    <div x-on:click.outside="renameFile = null" class="modal-card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-600">Rename file</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-900">Ubah nama file</h3>
            </div>
            <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="renameFile = null">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" :action="fileUpdateTemplate.replace('__FILE__', renameFile?.id ?? '')" class="mt-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="rename-file-name" class="mb-2 block text-sm font-semibold text-slate-700">File name</label>
                <input id="rename-file-name" type="text" name="name" x-model="renameFile.name" class="input-field" required>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" class="btn-secondary" @click="renameFile = null">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>
