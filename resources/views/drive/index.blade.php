@php
    $dashboardUrl = function (?int $folderId = null, array $extra = []) {
        return route('dashboard', array_filter(
            array_merge(['folder' => $folderId], $extra),
            fn ($value) => filled($value)
        ));
    };

    $formatBytes = function (?int $bytes): string {
        if ($bytes === null) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);

        if ($bytes === 0) {
            return '0 B';
        }

        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $power);
        $precision = $power === 0 ? 0 : ($value >= 100 ? 0 : ($value >= 10 ? 1 : 2));

        return number_format($value, $precision).' '.$units[$power];
    };

    $currentRootId = $breadcrumbs->first()?->id;
@endphp

<x-app-layout>
    <div
        class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
        x-data="{
            createFolderOpen: false,
            uploadOpen: false,
            renameFolder: null,
            renameFile: null,
            folderUpdateTemplate: @js(route('drive.folders.update', '__FOLDER__')),
            fileUpdateTemplate: @js(route('drive.files.update', '__FILE__')),
        }"
        x-on:keydown.escape.window="createFolderOpen = false; uploadOpen = false; renameFolder = null; renameFile = null"
    >
        @if (session('status'))
            <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-semibold">Please review the latest action.</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[288px_minmax(0,1fr)]">
            @include('drive.partials.sidebar')

            <section class="space-y-6">
                @include('drive.partials.hero')
                @include('drive.partials.folders')
                @include('drive.partials.files')
            </section>
        </div>

        @include('drive.partials.modals')
    </div>
</x-app-layout>
