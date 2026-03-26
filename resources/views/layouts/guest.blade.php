<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Transit Drive') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="app-shell flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-md">
                <a href="/" class="mx-auto mb-6 flex w-fit items-center gap-3">
                    <span class="flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-900 text-white shadow-lg shadow-slate-900/15">
                        <x-application-logo class="h-7 w-7 text-white" />
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Transit Drive</p>
                        <p class="text-sm text-slate-500">Private file workspace</p>
                    </div>
                </a>

                <div class="surface-panel overflow-hidden px-6 py-6 shadow-xl shadow-slate-900/5 sm:px-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
