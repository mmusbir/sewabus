@php
    $sticky = $sticky ?? true;
    $headerLogoLight = setting('header_logo_image');
    $headerLogoDark = setting('header_logo_image_dark');
    $isHome = request()->routeIs('home');
    $isKatalog = request()->routeIs('katalog.*');
    $isPackages = request()->routeIs('packages.*');
    $isContact = request()->routeIs('contact.*');
@endphp

<header x-data="{ open: false, darkMode: false, init() { const saved = localStorage.getItem('theme'); this.darkMode = saved ? saved === 'dark' : false; this.applyTheme(); }, applyTheme() { document.documentElement.classList.toggle('dark', this.darkMode); localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); } }" x-init="init()" x-effect="document.documentElement.classList.toggle('overflow-hidden', open); document.body.classList.toggle('overflow-hidden', open)" @class([
    'z-50 w-full border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md px-4 sm:px-6 lg:px-20 py-4',
    'sticky top-0' => $sticky,
])>
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center justify-center">
            @if($headerLogoLight || $headerLogoDark)
                <img
                    :src="darkMode ? @js($headerLogoDark ?: $headerLogoLight) : @js($headerLogoLight ?: $headerLogoDark)"
                    alt="Logo Header"
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
            <button type="button" class="hidden md:inline-flex items-center" @click="darkMode = !darkMode; applyTheme()" aria-label="Toggle dark mode">
                <span class="relative inline-flex h-8 w-16 items-center rounded-full border border-slate-200 bg-white shadow-inner transition-colors" :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'">
                    <span class="absolute left-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-500" :class="darkMode ? 'text-slate-200' : 'text-slate-500'">
                        <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-200" :class="darkMode ? 'text-slate-200' : 'text-slate-400'">
                        <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                    </span>
                    <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow transition-transform" :class="darkMode ? 'translate-x-8 bg-slate-200' : 'translate-x-1 bg-white'"></span>
                </span>
            </button>
            <button type="button" class="md:hidden text-slate-900 dark:text-slate-100" @click="open = !open" aria-label="Toggle menu" :aria-expanded="open">
                <x-fa-icon name="bars" class="fa-fw" />
            </button>
        </div>
    </div>

    <template x-teleport="body">
        <div x-cloak x-show="open" x-transition class="fixed inset-0 z-[9999] w-screen h-screen md:hidden bg-white/80 dark:bg-background-dark/80 backdrop-blur-2xl">
            <div class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
                <nav class="w-full max-w-sm rounded-2xl border border-slate-200/70 bg-white/80 dark:border-slate-700/70 dark:bg-slate-900/80 backdrop-blur-xl p-4 shadow-lg flex flex-col items-center gap-3 text-xl font-bold text-slate-800 dark:text-slate-100">
                    <a @class([
                        'w-full px-4 py-3 rounded-xl',
                        'bg-primary/15 text-primary' => $isHome,
                        'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isHome,
                    ]) href="/" @click="open = false">Beranda</a>
                    <a @class([
                        'w-full px-4 py-3 rounded-xl',
                        'bg-primary/15 text-primary' => $isKatalog,
                        'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isKatalog,
                    ]) href="{{ route('katalog.index') }}" @click="open = false">Armada</a>
                    <a @class([
                        'w-full px-4 py-3 rounded-xl',
                        'bg-primary/15 text-primary' => $isPackages,
                        'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isPackages,
                    ]) href="{{ route('packages.index') }}" @click="open = false">Paket Sewa</a>
                    <a @class([
                        'w-full px-4 py-3 rounded-xl',
                        'bg-primary/15 text-primary' => $isContact,
                        'bg-white/70 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-700/80' => !$isContact,
                    ]) href="{{ route('contact.index') }}" @click="open = false">Kontak</a>
                </nav>
                <div class="mt-8 flex flex-col items-center gap-4">
                    <button type="button" class="inline-flex items-center" @click="darkMode = !darkMode; applyTheme()" aria-label="Toggle dark mode">
                        <span class="relative inline-flex h-8 w-16 items-center rounded-full border border-slate-200 bg-white shadow-inner transition-colors" :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-200'">
                            <span class="absolute left-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-500" :class="darkMode ? 'text-slate-200' : 'text-slate-500'">
                                <x-fa-icon name="sun" style="regular" class="fa-fw text-[12px] leading-none" />
                            </span>
                            <span class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center justify-center text-slate-200" :class="darkMode ? 'text-slate-200' : 'text-slate-400'">
                                <x-fa-icon name="moon" style="regular" class="fa-fw text-[12px] leading-none" />
                            </span>
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow transition-transform" :class="darkMode ? 'translate-x-8 bg-slate-200' : 'translate-x-1 bg-white'"></span>
                        </span>
                    </button>
                    <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-300 dark:border-slate-700 p-3 text-slate-700 dark:text-slate-200" @click="open = false" aria-label="Close menu">
                        <x-fa-icon name="xmark" class="fa-fw" />
                    </button>
                </div>
            </div>
        </div>
    </template>
</header>
