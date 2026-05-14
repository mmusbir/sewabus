<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@php
    $seoTitle = setting('seo_packages_title', setting('seo_meta_title_default', setting('site_name', 'Sewa Bus Sulawesi Selatan')) . ' - Paket Sewa Bus');
    $seoDescription = setting('seo_packages_description', setting('seo_meta_description_default', 'Pilih paket sewa bus dan paket liburan untuk semua kabupaten/kota Sulawesi Selatan. Harga fleksibel sesuai rute, durasi, dan kebutuhan rombongan.'));
    $seoKeywords = setting('seo_meta_keywords_default', 'paket sewa bus sulawesi selatan, sewa bus wisata, paket bus rombongan makassar, rental bus bone');
    $seoCanonical = route('packages.index');
    $southSulawesiAreas = south_sulawesi_service_areas();
    $featuredServiceAreas = array_slice($southSulawesiAreas, 0, 9);
    $remainingServiceAreas = array_slice($southSulawesiAreas, 9);
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
@endphp
<title>{{ $seoTitle }}</title>
@include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoKeywords' => $seoKeywords, 'seoCanonical' => $seoCanonical])
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

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @if(!empty($databaseUnavailable))
        <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Data paket sementara tidak dapat dimuat karena koneksi database sedang bermasalah. Halaman tetap ditampilkan dengan data kosong.
        </div>
    @endif
    <div class="mb-10">
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-slate-100 mb-3">Paket Sewa Bus Sulawesi Selatan</h1>
        <p class="text-slate-600 dark:text-slate-400 max-w-3xl">Pilih paket perjalanan sesuai kebutuhan. Semua paket dapat disesuaikan dengan jumlah peserta, rute, dan fasilitas armada.</p>
    </div>

    <div class="flex border-b border-slate-200 dark:border-slate-800 mb-8 overflow-x-auto no-scrollbar">
        @foreach($typeTabs as $typeKey => $typeLabel)
            <a href="{{ route('packages.index', $typeKey === 'all' ? [] : ['type' => $typeKey]) }}" @class([
                'px-6 py-3 text-sm whitespace-nowrap border-b-4 transition-colors',
                'font-bold text-primary border-primary' => $selectedType === $typeKey,
                'font-medium text-slate-500 dark:text-slate-400 border-transparent hover:text-primary' => $selectedType !== $typeKey,
            ])>
                {{ $typeLabel }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
            @php
                $includeItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $package->includes ?? ''))));
                $badgeClasses = $package->type === 'liburan' ? 'bg-amber-500' : 'bg-emerald-600';
                $badgeText = $package->type === 'liburan' ? 'Paket Liburan' : 'Paket Sewa';
                $packageImage = $package->image_path ?: '/stitch_img_bus_shd.jpg';
                $packageThumb480 = media_thumbnail_url($packageImage, 480, 72) ?? $packageImage;
                $packageThumb640 = media_thumbnail_url($packageImage, 640, 75) ?? $packageImage;
                $packageThumb960 = media_thumbnail_url($packageImage, 960, 76) ?? $packageImage;
                $packageSrcset = implode(', ', [
                    $packageThumb480 . ' 480w',
                    $packageThumb640 . ' 640w',
                    $packageThumb960 . ' 960w',
                ]);
            @endphp
            <article class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="relative h-52 overflow-hidden">
                    <span class="absolute top-3 left-3 z-10 {{ $badgeClasses }} text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $badgeText }}</span>
                    <img
                        src="{{ $packageThumb640 }}"
                        srcset="{{ $packageSrcset }}"
                        sizes="(max-width: 768px) 100vw, (max-width: 1280px) 50vw, 33vw"
                        alt="{{ $package->title }}"
                        width="960"
                        height="540"
                        loading="lazy"
                        decoding="async"
                        class="h-full w-full object-cover"
                    >
                </div>
                <div class="p-5">
                    <h2 class="text-xl font-bold mb-2 text-slate-900 dark:text-slate-100">{{ $package->title }}</h2>
                    <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4">
                        @if($package->price_label)
                            <span class="inline-flex items-center gap-1"><x-fa-icon name="wallet" class="fa-fw text-base text-primary" />{{ $package->price_label }}</span>
                        @endif
                        @if($package->duration)
                            <span class="inline-flex items-center gap-1"><x-fa-icon name="clock" style="regular" class="fa-fw text-base text-primary" />{{ $package->duration }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-3 mb-4">{{ $package->description ?: 'Deskripsi paket belum tersedia.' }}</p>

                    @if(count($includeItems))
                        <ul class="space-y-1 mb-5">
                            @foreach(array_slice($includeItems, 0, 3) as $includeItem)
                                <li class="text-sm text-slate-600 dark:text-slate-300 flex items-start gap-2">
                                    <x-fa-icon name="circle-check" class="fa-fw text-primary text-sm mt-0.5" />
                                    <span>{{ $includeItem }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($whatsappNumber)
                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Halo Admin, saya tertarik dengan ' . $package->title) }}" target="_blank" rel="noopener" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                            Konsultasi Paket
                            <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                        </a>
                    @else
                        <button type="button" disabled class="w-full bg-primary/70 text-white/70 font-bold py-3 rounded-lg cursor-not-allowed flex items-center justify-center gap-2">
                            Kontak Belum Tersedia
                        </button>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-12 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-slate-500 dark:text-slate-400">Belum ada paket aktif yang tersedia.</p>
            </div>
        @endforelse
    </div>

    @if($packages->hasPages())
        <div class="mt-10">
            {{ $packages->onEachSide(1)->links() }}
        </div>
    @endif
</main>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 lg:p-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-3">Area Paket Sewa Bus di Semua Kabupaten/Kota Sulawesi Selatan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            @foreach($featuredServiceAreas as $serviceArea)
                <span class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $serviceArea }}</span>
            @endforeach
        </div>
        @if(count($remainingServiceAreas))
            <details class="mt-4">
                <summary class="cursor-pointer list-none text-sm font-bold text-primary inline-flex items-center gap-2">
                    Lihat Semua Area
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 mt-4">
                    @foreach($remainingServiceAreas as $serviceArea)
                        <span class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $serviceArea }}</span>
                    @endforeach
                </div>
            </details>
        @endif
    </div>
</section>

@include('partials.public.footer')
</body>
</html>
