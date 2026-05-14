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

        <nav class="hidden md:flex items-center gap-10">
            <a @class([
                'text-sm font-semibold transition-colors',
                'text-primary' => $isHome,
                'hover:text-primary' => !$isHome,
            ]) href="/">Beranda</a>
            <a @class([
                'text-sm font-semibold transition-colors',
                'text-primary' => $isKatalog,
                'hover:text-primary' => !$isKatalog,
            ]) href="{{ route('katalog.index') }}">Armada</a>
            <a @class([
                'text-sm font-semibold transition-colors',
                'text-primary' => $isPackages,
                'hover:text-primary' => !$isPackages,
            ]) href="{{ route('packages.index') }}">Paket Sewa</a>
            <a @class([
                'text-sm font-semibold transition-colors',
                'text-primary' => $isContact,
                'hover:text-primary' => !$isContact,
            ]) href="{{ route('contact.index') }}">Kontak</a>
        </nav>

        <div class="flex items-center gap-4">
            <button type="button" class="js-theme-toggle hidden md:inline-flex items-center" aria-label="Toggle dark mode">
                <span class="relative inline-flex h-8 w-16 items-center rounded-full border border-slate-200 bg-white shadow-inner transition-colors dark:border-slate-800 dark:bg-slate-900">
                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-500 dark:text-slate-200">
                        <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-400 dark:text-slate-200">
                        <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span data-theme-knob class="inline-block h-6 w-6 translate-x-1 transform rounded-full bg-white shadow transition-transform dark:bg-slate-200"></span>
                </span>
            </button>
            <button type="button" data-menu-toggle class="md:hidden text-slate-900 dark:text-slate-100" aria-label="Toggle menu" aria-expanded="false">
                <x-fa-icon name="bars" class="fa-fw" />
            </button>
        </div>
    </div>

    <div data-mobile-menu class="fixed inset-0 z-[9999] hidden w-screen h-screen md:hidden bg-white/80 dark:bg-background-dark/80 backdrop-blur-2xl">
        <div class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
            <nav class="w-full max-w-sm rounded-2xl border border-slate-200/70 bg-white/80 dark:border-slate-700/70 dark:bg-slate-900/80 backdrop-blur-xl p-4 shadow-lg flex flex-col items-center gap-3 text-xl font-bold text-slate-800 dark:text-slate-100">
                <a @class([
                    'w-full px-4 py-3 rounded-xl',
                    'bg-primary/15 text-primary' => $isHome,
                    'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isHome,
                ]) href="/" data-menu-close>Beranda</a>
                <a @class([
                    'w-full px-4 py-3 rounded-xl',
                    'bg-primary/15 text-primary' => $isKatalog,
                    'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isKatalog,
                ]) href="{{ route('katalog.index') }}" data-menu-close>Armada</a>
                <a @class([
                    'w-full px-4 py-3 rounded-xl',
                    'bg-primary/15 text-primary' => $isPackages,
                    'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isPackages,
                ]) href="{{ route('packages.index') }}" data-menu-close>Paket Sewa</a>
                <a @class([
                    'w-full px-4 py-3 rounded-xl',
                    'bg-primary/15 text-primary' => $isContact,
                    'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isContact,
                ]) href="{{ route('contact.index') }}" data-menu-close>Kontak</a>
            </nav>
            <div class="mt-8 flex flex-col items-center gap-4">
                <button type="button" class="js-theme-toggle inline-flex items-center" aria-label="Toggle dark mode">
                    <span class="relative inline-flex h-8 w-16 items-center rounded-full border border-slate-200 bg-white shadow-inner transition-colors dark:border-slate-800 dark:bg-slate-900">
                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-500 dark:text-slate-200">
                            <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                        </span>
                        <span class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-400 dark:text-slate-200">
                            <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                        </span>
                        <span data-theme-knob class="inline-block h-6 w-6 translate-x-1 transform rounded-full bg-white shadow transition-transform dark:bg-slate-200"></span>
                    </span>
                </button>
                <button type="button" data-menu-close class="inline-flex items-center justify-center rounded-full border border-slate-300 dark:border-slate-700 p-3 text-slate-700 dark:text-slate-200" aria-label="Close menu">
                    <x-fa-icon name="xmark" class="fa-fw" />
                </button>
            </div>
        </div>
    </div>
</header>
