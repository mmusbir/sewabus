<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@php
    $siteName = setting('site_name', 'Sewa Bus Sulawesi Selatan');
    $seoTitle = trim($package->title . ' - ' . $siteName);
    $seoDescription = $package->description ?: setting('seo_meta_description_default', 'Paket perjalanan bus pariwisata yang dapat disesuaikan dengan kebutuhan rombongan Anda.');
    $seoKeywords = setting('seo_meta_keywords_default', 'paket sewa bus, paket liburan, rental bus sulawesi selatan');
    $seoCanonical = route('packages.show', $package);
    $seoImage = $package->image_path ?: '/stitch_img_bus_shd.jpg';
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $packageTypeLabel = $package->type === 'liburan' ? 'Paket Liburan' : 'Paket Sewa';
    $packageTypeBadgeClass = $package->type === 'liburan' ? 'bg-amber-500' : 'bg-emerald-600';
    $includeItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $package->includes ?? ''))));
    $excludeItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $package->excludes ?? ''))));
    $termItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $package->terms_conditions ?? ''))));
    $itineraryItems = collect($package->itinerary ?? [])
        ->map(function ($item, int $index) {
            $day = trim((string) data_get($item, 'day', 'Day ' . ($index + 1)));
            $description = trim((string) data_get($item, 'description', ''));

            if ($day === '' && $description === '') {
                return null;
            }

            return [
                'day' => $day !== '' ? $day : 'Day ' . ($index + 1),
                'description' => $description,
            ];
        })
        ->filter()
        ->values()
        ->all();
    $packageImage = $package->image_path ?: '/stitch_img_bus_shd.jpg';
    $image480 = media_thumbnail_url($packageImage, 480, 72) ?? $packageImage;
    $image768 = media_thumbnail_url($packageImage, 768, 74) ?? $packageImage;
    $image1280 = media_thumbnail_url($packageImage, 1280, 78) ?? $packageImage;
@endphp
<title>{{ $seoTitle }}</title>
@include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoKeywords' => $seoKeywords, 'seoCanonical' => $seoCanonical, 'seoImage' => $seoImage])
<link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
@vite(['resources/css/app.css', 'resources/js/public.js'])
@include('partials.public.plus-jakarta-fonts')
<style>
    :root {
        --color-primary: 225 106 55;
        --color-secondary-green: 1 128 61;
        --color-background-light: 248 246 246;
        --color-background-dark: 33 22 17;
        --font-display: "Plus Jakarta Sans";
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen">
@include('partials.public.header', ['sticky' => false])

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
    <nav class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('packages.index') }}" class="hover:text-primary">Paket Sewa</a>
        <span class="mx-2">/</span>
        <span class="text-slate-700 dark:text-slate-200">{{ $package->title }}</span>
    </nav>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-7 lg:gap-10 items-start">
        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
            <div class="relative aspect-[16/10]">
                <span class="absolute top-4 left-4 z-10 {{ $packageTypeBadgeClass }} text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $packageTypeLabel }}</span>
                <img
                    src="{{ $image768 }}"
                    srcset="{{ $image480 }} 480w, {{ $image768 }} 768w, {{ $image1280 }} 1280w"
                    sizes="(max-width: 1024px) 100vw, 50vw"
                    alt="{{ $package->title }}"
                    width="1280"
                    height="800"
                    class="h-full w-full object-cover"
                    decoding="async"
                    fetchpriority="high"
                >
            </div>
        </div>

        <div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-slate-100">{{ $package->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                @if(filled($package->price_label))
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 font-bold text-primary">
                        <x-fa-icon name="wallet" class="fa-fw text-sm" />
                        {{ $package->price_label }}
                    </span>
                @endif
                @if(filled($package->duration))
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1 font-semibold text-slate-700 dark:text-slate-200">
                        <x-fa-icon name="clock" style="regular" class="fa-fw text-sm text-primary" />
                        {{ $package->duration }}
                    </span>
                @endif
            </div>
            <p class="mt-5 text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $package->description ?: 'Deskripsi paket belum tersedia.' }}</p>

            <div class="mt-7 space-y-3">
                @if($whatsappNumber)
                    <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Halo Admin, saya tertarik dengan paket ' . $package->title) }}" target="_blank" rel="noopener" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 font-bold text-white hover:bg-primary/90 transition-colors">
                        Konsultasi Paket Ini
                        <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                    </a>
                @endif
                <a href="{{ route('packages.index', ['type' => $package->type]) }}" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl border border-slate-300 dark:border-slate-700 px-5 py-3 font-semibold text-slate-700 dark:text-slate-200 hover:border-primary hover:text-primary transition-colors">
                    Lihat Paket {{ $packageTypeLabel === 'Paket Liburan' ? 'Liburan' : 'Sewa' }} Lainnya
                </a>
            </div>
        </div>
    </section>

    <section class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-5">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Yang Sudah Termasuk</h2>
            @if(count($includeItems))
                <ul class="space-y-2">
                    @foreach($includeItems as $includeItem)
                        <li class="text-sm text-slate-600 dark:text-slate-300 flex items-start gap-2">
                            <x-fa-icon name="circle-check" class="fa-fw text-primary text-sm mt-0.5" />
                            <span>{{ $includeItem }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada daftar include untuk paket ini.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-5">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Yang Tidak Termasuk</h2>
            @if(count($excludeItems))
                <ul class="space-y-2">
                    @foreach($excludeItems as $excludeItem)
                        <li class="text-sm text-slate-600 dark:text-slate-300 flex items-start gap-2">
                            <x-fa-icon name="circle-xmark" style="regular" class="fa-fw text-rose-500 text-sm mt-0.5" />
                            <span>{{ $excludeItem }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada daftar exclude untuk paket ini.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-5">
            <h2 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Syarat &amp; Ketentuan</h2>
            @if(count($termItems))
                <ul class="space-y-2">
                    @foreach($termItems as $termItem)
                        <li class="text-sm text-slate-600 dark:text-slate-300 flex items-start gap-2">
                            <x-fa-icon name="circle-info" class="fa-fw text-sky-500 text-sm mt-0.5" />
                            <span>{{ $termItem }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada syarat dan ketentuan untuk paket ini.</p>
            @endif
        </div>
    </section>

    @if(count($itineraryItems))
        <section class="mt-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 p-5 sm:p-6">
            <h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-4">Itinerary Perjalanan</h2>
            <div class="space-y-3">
                @foreach($itineraryItems as $itineraryItem)
                    <article class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 p-4">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-primary mb-1">{{ $itineraryItem['day'] }}</h3>
                        @if($itineraryItem['description'] !== '')
                            <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $itineraryItem['description'] }}</p>
                        @else
                            <p class="text-sm text-slate-500 dark:text-slate-400">Detail itinerary belum diisi.</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if($relatedPackages->isNotEmpty())
        <section class="mt-10">
            <h2 class="text-2xl font-black text-slate-900 dark:text-slate-100 mb-5">Paket Serupa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach($relatedPackages as $relatedPackage)
                    @php
                        $relatedImage = $relatedPackage->image_path ?: '/stitch_img_bus_shd.jpg';
                        $relatedThumb = media_thumbnail_url($relatedImage, 640, 75) ?? $relatedImage;
                    @endphp
                    <article class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50">
                        <img src="{{ $relatedThumb }}" alt="{{ $relatedPackage->title }}" width="640" height="400" class="h-44 w-full object-cover" loading="lazy" decoding="async">
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-2">{{ $relatedPackage->title }}</h3>
                            <a href="{{ route('packages.show', $relatedPackage) }}" class="text-sm font-bold text-primary inline-flex items-center gap-2 hover:underline">
                                Lihat Detail
                                <x-fa-icon name="arrow-right" class="fa-fw text-xs" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</main>

@include('partials.public.footer')
</body>
</html>

