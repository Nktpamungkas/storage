<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Transit Drive') }}</title>
        <meta name="theme-color" content="#0f172a">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="app-shell">
            @include('layouts.navigation')

            @isset($header)
                <header class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
                    <div class="surface-panel px-6 py-5">
                        <div class="text-lg font-semibold text-slate-900">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <main class="pb-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
