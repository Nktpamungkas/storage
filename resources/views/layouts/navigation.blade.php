@php
    use Illuminate\Support\Str;
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur-xl">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm md:hidden"
                @click="open = !open"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-900/15">
                    <x-application-logo class="h-6 w-6 text-white" />
                </span>
                <div class="hidden sm:block">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">Transit Drive</p>
                    <p class="text-sm text-slate-500">Private file workspace</p>
                </div>
            </a>
        </div>

        @if (request()->routeIs('dashboard'))
            <div class="hidden flex-1 md:flex">
                <form method="GET" action="{{ route('dashboard') }}" class="mx-auto w-full max-w-xl">
                    @if (request('folder'))
                        <input type="hidden" name="folder" value="{{ request('folder') }}">
                    @endif
                    <label class="relative block">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </span>
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search inside the current folder"
                            class="input-field !rounded-full !border-slate-200/80 !bg-slate-100/80 pl-12"
                        >
                    </label>
                </form>
            </div>
        @endif

        <div class="hidden items-center gap-3 md:flex">
            <a href="{{ route('dashboard') }}" class="btn-secondary {{ request()->routeIs('dashboard') ? '!border-sky-100 !bg-sky-50 !text-sky-700' : '' }}">
                My Drive
            </a>
            <a href="{{ route('profile.edit') }}" class="btn-secondary {{ request()->routeIs('profile.*') ? '!border-sky-100 !bg-sky-50 !text-sky-700' : '' }}">
                Profile
            </a>

            <div class="flex items-center gap-3 rounded-full border border-slate-200/80 bg-white px-2 py-2 shadow-sm">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                    {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="pr-2">
                    <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary !px-4 !py-2">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" class="border-t border-slate-200/70 bg-white/95 px-4 py-4 md:hidden">
        <div class="mx-auto max-w-7xl space-y-4">
            @if (request()->routeIs('dashboard'))
                <form method="GET" action="{{ route('dashboard') }}">
                    @if (request('folder'))
                        <input type="hidden" name="folder" value="{{ request('folder') }}">
                    @endif
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search inside the current folder"
                        class="input-field"
                    >
                </form>
            @endif

            <div class="grid gap-2">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5h3.171a2 2 0 0 1 1.414.586l1.329 1.328a2 2 0 0 0 1.414.586H18.5A2.5 2.5 0 0 1 21 10v8.5A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5v-11Z" />
                        </svg>
                    </span>
                    My Drive
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'sidebar-link-active' : '' }}">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </span>
                    Profile
                </a>
            </div>

            <div class="rounded-3xl border border-slate-200/70 bg-slate-50 p-4">
                <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn-secondary w-full justify-center">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
