@php
    $navBase = 'flex items-center rounded-lg px-3 py-2 transition-colors';
    $navIdle = 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium';
    $navActive = 'bg-primary/10 text-primary font-bold';
@endphp
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Panel Admin')</title>
    <link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.fontawesome')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <style>
        :root {
            --color-primary: 1 128 61;
            --color-secondary: 225 105 55;
            --color-background-light: 245 248 247;
            --color-background-dark: 15 35 24;
            --font-display: "Inter";
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body
    x-data="{
        sidebarOpen: false,
        sidebarCompact: localStorage.getItem('admin_sidebar_compact') === '1',
        init() {
            this.sidebarOpen = window.innerWidth >= 768;
            window.addEventListener('resize', () => {
                this.sidebarOpen = window.innerWidth >= 768;
            });
        },
        closeSidebarOnMobile() { if (window.innerWidth < 768) this.sidebarOpen = false; },
        toggleCompact() {
            this.sidebarCompact = !this.sidebarCompact;
            localStorage.setItem('admin_sidebar_compact', this.sidebarCompact ? '1' : '0');
        }
    }"
    x-init="init()"
    class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100"
>
    <div class="min-h-screen flex">
        <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/35 md:hidden" @click="sidebarOpen = false"></div>

        <aside
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
                sidebarCompact ? 'md:w-20' : 'md:w-64'
            ]"
            class="fixed md:sticky top-0 inset-y-0 left-0 z-50 h-screen w-64 md:shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 ease-in-out overflow-hidden"
        >
            <div class="h-full p-3 lg:p-4 flex flex-col">
                <div class="mb-4 lg:mb-5">
                    <div class="h-12 flex items-center" :class="sidebarCompact ? 'justify-center' : 'justify-between gap-3'">
                        <a href="{{ route('admin.dashboard') }}" x-show="!sidebarCompact" x-cloak class="flex items-center gap-3">
                            @if(setting('header_logo_image'))
                                <img src="{{ setting('header_logo_image') }}" alt="Logo Header" class="h-9 object-contain max-w-[150px]">
                            @else
                                <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white">
                                    <x-fa-icon name="bus" class="fa-fw text-[20px]" />
                                </div>
                                <span class="font-extrabold tracking-tight" x-show="!sidebarCompact" x-cloak>CahayaBone</span>
                            @endif
                        </a>

                        <button type="button" class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200" @click="sidebarOpen = false" aria-label="Tutup sidebar">
                            <x-fa-icon name="xmark" class="fa-fw text-[20px]" />
                        </button>
                    </div>
                </div>

                <nav class="flex-1 min-h-0 overflow-y-auto space-y-1 pr-1">
                    <a href="{{ route('admin.dashboard') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.dashboard') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Dashboard">
                        <x-fa-icon name="gauge-high" class="fa-fw text-[20px]" />
                        <span x-show="!sidebarCompact" x-cloak>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.galleries.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.galleries.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Galeri Armada">
                        <x-fa-icon name="images" class="fa-fw text-[20px]" />
                        <span x-show="!sidebarCompact" x-cloak>Galeri Armada</span>
                    </a>

                    <a href="{{ route('admin.rental-packages.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.rental-packages.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Paket Sewa">
                        <x-fa-icon name="route" class="fa-fw text-[20px]" />
                        <span x-show="!sidebarCompact" x-cloak>Paket Sewa</span>
                    </a>

                    @if(auth()->user()->canAccessSettings() || auth()->user()->canManageUsers())
                        <div class="pt-2" x-show="!sidebarCompact" x-cloak>
                            <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengaturan</p>
                        </div>

                        @if(auth()->user()->canAccessSettings())
                            <a href="{{ route('admin.settings.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.index') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="General">
                                <x-fa-icon name="sliders" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>General</span>
                            </a>

                            <a href="{{ route('admin.settings.categories.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.categories.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Kategori">
                                <x-fa-icon name="tags" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>Kategori</span>
                            </a>

                            <a href="{{ route('admin.settings.po.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.po.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Nama PO">
                                <x-fa-icon name="building" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>Nama PO</span>
                            </a>

                            <a href="{{ route('admin.settings.facilities.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.facilities.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Fasilitas">
                                <x-fa-icon name="list-check" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>Fasilitas</span>
                            </a>

                            <a href="{{ route('admin.settings.seo.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.seo.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="SEO">
                                <x-fa-icon name="chart-line" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>SEO</span>
                            </a>
                        @endif

                        @if(auth()->user()->canManageUsers())
                            <a href="{{ route('admin.settings.users.index') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('admin.settings.users.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Manajemen Akun">
                                <x-fa-icon name="users-gear" class="fa-fw text-[20px]" />
                                <span x-show="!sidebarCompact" x-cloak>Manajemen Akun</span>
                            </a>
                        @endif
                    @endif
                </nav>

                <div class="mt-auto shrink-0 pt-3 border-t border-slate-200 dark:border-slate-800 space-y-1.5">
                    <a href="{{ route('profile.edit') }}" @click="closeSidebarOnMobile()" class="{{ $navBase }} {{ request()->routeIs('profile.*') ? $navActive : $navIdle }}" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Profil Saya">
                        <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="Foto Profil" class="w-7 h-7 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                        <span x-show="!sidebarCompact" x-cloak>Profil Saya</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full {{ $navBase }} text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800" :class="sidebarCompact ? 'justify-center' : 'gap-3'" title="Keluar">
                            <x-fa-icon name="right-from-bracket" class="fa-fw text-[20px]" />
                            <span class="font-semibold" x-show="!sidebarCompact" x-cloak>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 min-w-0 flex flex-col">
            <header class="sticky top-0 z-30 h-16 border-b border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 md:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Buka sidebar">
                        <x-fa-icon name="bars" class="fa-fw text-[20px]" />
                    </button>
                    <button type="button" class="hidden md:inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800" @click="toggleCompact()" aria-label="Ringkas sidebar">
                        <x-fa-icon name="angles-right" class="text-[18px]" x-show="sidebarCompact" x-cloak />
                        <x-fa-icon name="angles-left" class="text-[18px]" x-show="!sidebarCompact" x-cloak />
                    </button>
                    <h2 class="text-base sm:text-lg font-bold truncate">@yield('header_title')</h2>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 sm:gap-3 rounded-lg px-2 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] uppercase mt-1 text-slate-500">{{ auth()->user()->roleLabel() }}</p>
                    </div>
                    <img src="{{ auth()->user()->profilePhotoUrl() }}" alt="Foto Profil" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                </a>
            </header>

            <section class="p-4 sm:p-5 lg:p-6">
                <div class="mx-auto w-full max-w-[1280px] space-y-6">
                    @if(session('success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm font-semibold flex items-center gap-2">
                            <x-fa-icon name="circle-check" class="fa-fw text-[18px]" />
                            {{ session('success') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </main>
    </div>
</body>
</html>
