@php
    $sticky = $sticky ?? true;
    $headerLogoLight = setting('header_logo_image');
    $headerLogoDark = setting('header_logo_image_dark');
    $headerLogoLightOptimized = media_thumbnail_url($headerLogoLight, 320, 82) ?? $headerLogoLight;
    $headerLogoDarkOptimized = media_thumbnail_url($headerLogoDark, 320, 82) ?? $headerLogoDark;
    $isHome = request()->routeIs('home');
    $isKatalog = request()->routeIs('katalog.*');
    $isPackages = request()->routeIs('packages.*');
    $isContact = request()->routeIs('contact.*');
@endphp

<header id="public-header" @class([
    'z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md px-4 sm:px-6 lg:px-20 py-4',
    'sticky top-0' => $sticky,
])>
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center justify-center">
            @if($headerLogoLight || $headerLogoDark)
                <img
                    src="{{ $headerLogoLightOptimized ?: $headerLogoDarkOptimized }}"
                    data-logo-light="{{ $headerLogoLightOptimized ?: $headerLogoDarkOptimized }}"
                    data-logo-dark="{{ $headerLogoDarkOptimized ?: $headerLogoLightOptimized }}"
                    alt="Logo Header"
                    width="160"
                    height="40"
                    class="h-10 object-contain"
                >
            @endif

            @if(setting('header_logo_show_text', true) && !($headerLogoLight || $headerLogoDark))
                <div class="text-primary mr-3">
                    <x-fa-icon name="bus" class="fa-fw text-4xl" />
                </div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">{{ setting('header_logo_text', 'BusPariwisata') }}</h2>
            @endif
        </div>

        <nav class="hidden md:flex items-center gap-1 rounded-full border border-slate-200/80 bg-white/70 p-1 shadow-sm shadow-slate-900/5 backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-950/40">
            <a @class([
                'relative rounded-full px-4 py-2 text-sm font-bold transition-all duration-200',
                'bg-primary text-white shadow-md shadow-primary/20' => $isHome,
                'text-slate-600 hover:bg-primary/10 hover:text-primary dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' => !$isHome,
            ]) href="/">Beranda</a>
            <a @class([
                'relative rounded-full px-4 py-2 text-sm font-bold transition-all duration-200',
                'bg-primary text-white shadow-md shadow-primary/20' => $isKatalog,
                'text-slate-600 hover:bg-primary/10 hover:text-primary dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' => !$isKatalog,
            ]) href="{{ route('katalog.index') }}">Armada</a>
            <a @class([
                'relative rounded-full px-4 py-2 text-sm font-bold transition-all duration-200',
                'bg-primary text-white shadow-md shadow-primary/20' => $isPackages,
                'text-slate-600 hover:bg-primary/10 hover:text-primary dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' => !$isPackages,
            ]) href="{{ route('packages.index') }}">Paket Sewa</a>
            <a @class([
                'relative rounded-full px-4 py-2 text-sm font-bold transition-all duration-200',
                'bg-primary text-white shadow-md shadow-primary/20' => $isContact,
                'text-slate-600 hover:bg-primary/10 hover:text-primary dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white' => !$isContact,
            ]) href="{{ route('contact.index') }}">Kontak</a>
        </nav>

        <div class="flex items-center gap-3">
            <button type="button" class="js-theme-toggle hidden md:inline-flex items-center" aria-label="Toggle dark mode" aria-pressed="false">
                <span class="relative inline-flex h-10 w-20 items-center rounded-full border border-slate-200 bg-white p-1 shadow-inner transition-colors dark:border-slate-800 dark:bg-slate-900">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 inline-flex size-4 items-center justify-center text-slate-500 dark:text-slate-200">
                        <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 inline-flex size-4 items-center justify-center text-slate-400 dark:text-slate-200">
                        <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span data-theme-knob class="pointer-events-none inline-block size-8 translate-x-0 transform rounded-full bg-white shadow-[0_2px_10px_rgba(15,23,42,0.16)] transition-transform duration-200 dark:bg-slate-200"></span>
                </span>
            </button>
            <button type="button" data-menu-toggle class="md:hidden inline-flex size-11 items-center justify-center rounded-full border border-slate-200 bg-white/90 text-slate-900 shadow-sm transition hover:border-primary hover:text-primary dark:border-slate-800 dark:bg-slate-900/70 dark:text-slate-100" aria-label="Toggle menu" aria-expanded="false">
                <x-fa-icon name="bars" class="fa-fw" />
            </button>
        </div>
    </div>

    <div data-mobile-menu class="fixed inset-0 z-[10020] hidden md:hidden bg-slate-950/35 backdrop-blur-sm" aria-hidden="true">
        <div class="flex h-[100dvh] items-stretch justify-end p-3 sm:p-4">
            <div data-mobile-menu-panel class="flex h-full w-full max-w-sm flex-col overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-2xl dark:border-slate-800 dark:bg-background-dark">
                <div class="flex items-center justify-between border-b border-slate-200/70 px-5 py-4 dark:border-slate-800">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Menu</p>
                        <p class="text-sm font-bold text-slate-900 dark:text-slate-100">Navigasi cepat</p>
                    </div>
                    <button type="button" data-menu-close class="inline-flex size-10 items-center justify-center rounded-full border border-slate-200 text-slate-700 transition hover:border-primary hover:text-primary dark:border-slate-700 dark:text-slate-200" aria-label="Tutup menu">
                        <x-fa-icon name="xmark" class="fa-fw" />
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto px-4 py-4 text-xl font-bold text-slate-800 dark:text-slate-100">
                <a @class([
                    'flex w-full min-h-14 items-center rounded-2xl px-4 py-3 text-left transition active:scale-[0.99] touch-manipulation',
                    'bg-primary/15 text-primary' => $isHome,
                    'bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-800/80' => !$isHome,
                ]) href="/" data-menu-close>Beranda</a>
                <a @class([
                    'mt-2 flex w-full min-h-14 items-center rounded-2xl px-4 py-3 text-left transition active:scale-[0.99] touch-manipulation',
                    'bg-primary/15 text-primary' => $isKatalog,
                    'bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-800/80' => !$isKatalog,
                ]) href="{{ route('katalog.index') }}" data-menu-close>Armada</a>
                <a @class([
                    'mt-2 flex w-full min-h-14 items-center rounded-2xl px-4 py-3 text-left transition active:scale-[0.99] touch-manipulation',
                    'bg-primary/15 text-primary' => $isPackages,
                    'bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-800/80' => !$isPackages,
                ]) href="{{ route('packages.index') }}" data-menu-close>Paket Sewa</a>
                <a @class([
                    'mt-2 flex w-full min-h-14 items-center rounded-2xl px-4 py-3 text-left transition active:scale-[0.99] touch-manipulation',
                    'bg-primary/15 text-primary' => $isContact,
                    'bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-800/80' => !$isContact,
                ]) href="{{ route('contact.index') }}" data-menu-close>Kontak</a>
                </nav>
                <div class="border-t border-slate-200/70 px-4 py-4 dark:border-slate-800">
                    <button type="button" class="js-theme-toggle inline-flex w-full items-center justify-center" aria-label="Toggle dark mode" aria-pressed="false">
                        <span class="relative inline-flex h-10 w-20 items-center rounded-full border border-slate-200 bg-white p-1 shadow-inner transition-colors dark:border-slate-800 dark:bg-slate-900">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 inline-flex size-4 items-center justify-center text-slate-500 dark:text-slate-200">
                                <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                            </span>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 inline-flex size-4 items-center justify-center text-slate-400 dark:text-slate-200">
                                <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                            </span>
                            <span data-theme-knob class="pointer-events-none inline-block size-8 translate-x-0 transform rounded-full bg-white shadow-[0_2px_10px_rgba(15,23,42,0.16)] transition-transform duration-200 dark:bg-slate-200"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
