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
    $relatedSectionTitle = $package->type === 'liburan' ? 'Paket Liburan Lainnya' : 'Paket Sewa Lainnya';
    $liburanGalleryItems = collect([
        [
            'label' => 'Unit Kendaraan - Luar',
            'url' => $package->vehicle_exterior_image_path,
        ],
        [
            'label' => 'Unit Kendaraan - Dalam',
            'url' => $package->vehicle_interior_image_path,
        ],
        [
            'label' => 'Penginapan - Luar',
            'url' => $package->lodging_exterior_image_path,
        ],
        [
            'label' => 'Penginapan - Dalam',
            'url' => $package->lodging_interior_image_path,
        ],
    ])->filter(fn (array $item) => filled($item['url']))->values()->all();
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

    html {
        scroll-behavior: smooth;
    }

    .package-detail-surface {
        background:
            linear-gradient(135deg, rgba(225, 106, 55, 0.08), rgba(1, 128, 61, 0.08)),
            linear-gradient(180deg, rgba(255, 255, 255, 0.82), rgba(248, 246, 246, 0.96));
    }

    .dark .package-detail-surface {
        background:
            linear-gradient(135deg, rgba(225, 106, 55, 0.16), rgba(1, 128, 61, 0.12)),
            linear-gradient(180deg, rgba(33, 22, 17, 0.92), rgba(15, 23, 42, 0.95));
    }

    @media (prefers-reduced-motion: no-preference) {
        .detail-reveal {
            animation: detailReveal 520ms ease both;
        }

        .detail-reveal-delay {
            animation-delay: 110ms;
        }

        @keyframes detailReveal {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen">
@include('partials.public.header', ['sticky' => false])

<main>
    <section class="package-detail-surface border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-7 sm:py-10">
            <nav class="mb-5 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
                <span>/</span>
                <a href="{{ route('packages.index') }}" class="hover:text-primary">Paket</a>
                <span>/</span>
                <span class="max-w-full truncate text-slate-700 dark:text-slate-200">{{ $package->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 lg:gap-10 items-center">
                <div class="detail-reveal lg:col-span-7">
                    <div class="relative overflow-hidden rounded-2xl border border-white/70 bg-white shadow-2xl shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900 dark:shadow-black/20">
                        <div class="relative aspect-[16/10]">
                            <span class="absolute top-4 left-4 z-10 {{ $packageTypeBadgeClass }} text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-lg shadow-black/15">{{ $packageTypeLabel }}</span>
                            <img
                                src="{{ $image768 }}"
                                srcset="{{ $image480 }} 480w, {{ $image768 }} 768w, {{ $image1280 }} 1280w"
                                sizes="(max-width: 1024px) 100vw, 58vw"
                                alt="{{ $package->title }}"
                                width="1280"
                                height="800"
                                class="h-full w-full object-cover"
                                decoding="async"
                                fetchpriority="high"
                            >
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/75 via-slate-950/20 to-transparent p-4 sm:p-5">
                                <div class="grid grid-cols-3 gap-2 text-white">
                                    <div class="rounded-lg bg-white/15 px-3 py-2 backdrop-blur-md">
                                        <p class="text-[10px] font-bold uppercase text-white/70">Durasi</p>
                                        <p class="mt-1 text-sm font-black">{{ $package->duration ?: '-' }}</p>
                                    </div>
                                    <div class="rounded-lg bg-white/15 px-3 py-2 backdrop-blur-md">
                                        <p class="text-[10px] font-bold uppercase text-white/70">Include</p>
                                        <p class="mt-1 text-sm font-black">{{ count($includeItems) }} item</p>
                                    </div>
                                    <div class="rounded-lg bg-white/15 px-3 py-2 backdrop-blur-md">
                                        <p class="text-[10px] font-bold uppercase text-white/70">Itinerary</p>
                                        <p class="mt-1 text-sm font-black">{{ count($itineraryItems) }} day</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-reveal detail-reveal-delay lg:col-span-5">
                    <p class="mb-3 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white/70 px-3 py-1 text-[11px] font-black uppercase tracking-wide text-primary shadow-sm dark:border-primary/30 dark:bg-slate-900/70">
                        <x-fa-icon name="route" class="fa-fw text-xs" />
                        Detail {{ $packageTypeLabel }}
                    </p>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight text-slate-950 dark:text-white">{{ $package->title }}</h1>
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                        @if(filled($package->price_label))
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary px-3 py-1.5 font-bold text-white shadow-lg shadow-primary/20">
                                <x-fa-icon name="wallet" class="fa-fw text-sm" />
                                {{ $package->price_label }}
                            </span>
                        @endif
                        @if(filled($package->duration))
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                                <x-fa-icon name="clock" style="regular" class="fa-fw text-sm text-primary" />
                                {{ $package->duration }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-5 text-base leading-8 text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $package->description ?: 'Deskripsi paket belum tersedia.' }}</p>

                    <div class="mt-6 flex flex-wrap items-center gap-2">
                        <a href="#detail-paket" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-xs font-bold text-slate-700 transition hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                            <x-fa-icon name="list-check" class="fa-fw text-xs" />
                            Include / Exclude
                        </a>
                        <a href="#itinerary-paket" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-xs font-bold text-slate-700 transition hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                            <x-fa-icon name="map-location-dot" class="fa-fw text-xs" />
                            Itinerary
                        </a>
                        @if($relatedPackages->isNotEmpty())
                            <a href="#paket-lainnya" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white/80 px-4 py-2 text-xs font-bold text-slate-700 transition hover:border-primary hover:text-primary dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                                <x-fa-icon name="images" class="fa-fw text-xs" />
                                Paket Lainnya
                            </a>
                        @endif
                    </div>

                    <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('Halo Admin, saya tertarik dengan paket ' . $package->title) }}" target="_blank" rel="noopener" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 font-black text-white shadow-xl shadow-primary/25 transition hover:bg-primary/90 active:scale-[0.99]">
                                Konsultasi Paket Ini
                                <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-base" />
                            </a>
                        @endif
                        <a href="{{ route('packages.index', ['type' => $package->type]) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white/80 px-5 py-3 font-bold text-slate-700 transition hover:border-primary hover:text-primary active:scale-[0.99] dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200">
                            Paket Lainnya
                            <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="detail-paket" class="bg-white py-8 sm:py-10 dark:bg-background-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-primary">Detail benefit</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950 dark:text-white">Apa saja yang kamu dapatkan</h2>
                </div>
                <p class="max-w-xl text-sm text-slate-500 dark:text-slate-400">Ringkasan ini membantu calon penyewa cepat memahami cakupan paket sebelum konsultasi.</p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/50 p-5">
            <div class="mb-4 inline-flex size-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                <x-fa-icon name="circle-check" class="fa-fw" />
            </div>
            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Yang Sudah Termasuk</h3>
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

        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/50 p-5">
            <div class="mb-4 inline-flex size-10 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300">
                <x-fa-icon name="circle-xmark" style="regular" class="fa-fw" />
            </div>
            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Yang Tidak Termasuk</h3>
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

        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/50 p-5">
            <div class="mb-4 inline-flex size-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">
                <x-fa-icon name="circle-info" class="fa-fw" />
            </div>
            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-3">Syarat &amp; Ketentuan</h3>
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
            </div>
        </div>
    </section>

    @if($package->type === 'liburan' && count($liburanGalleryItems))
        <section class="bg-background-light py-8 sm:py-10 dark:bg-background-dark/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-primary">Dokumentasi paket</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-950 dark:text-white">Foto Unit & Penginapan</h2>
                    </div>
                    <p class="max-w-xl text-sm text-slate-500 dark:text-slate-400">Galeri ini khusus paket liburan untuk memberi gambaran unit kendaraan dan penginapan.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($liburanGalleryItems as $galleryItem)
                        @php
                            $galleryThumb = media_thumbnail_url($galleryItem['url'], 640, 76) ?? $galleryItem['url'];
                        @endphp
                        <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                            <img
                                src="{{ $galleryThumb }}"
                                alt="{{ $galleryItem['label'] }}"
                                width="640"
                                height="420"
                                class="h-40 w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            >
                            <figcaption class="px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-200">{{ $galleryItem['label'] }}</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(count($itineraryItems))
        <section id="itinerary-paket" class="bg-background-light py-8 sm:py-10 dark:bg-background-dark/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-wide text-primary">Rencana perjalanan</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950 dark:text-white">Itinerary Perjalanan</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Alur perjalanan ditampilkan per hari agar mudah dibaca di mobile maupun desktop.</p>
                </div>
            <div class="relative space-y-4 before:absolute before:left-5 before:top-2 before:bottom-2 before:w-px before:bg-slate-200 dark:before:bg-slate-800">
                @foreach($itineraryItems as $itineraryItem)
                    <article class="relative pl-14">
                        <div class="absolute left-0 top-0 z-10 inline-flex size-10 items-center justify-center rounded-full bg-primary text-sm font-black text-white shadow-lg shadow-primary/20">
                            {{ $loop->iteration }}
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/70">
                            <h3 class="text-sm font-extrabold uppercase tracking-wide text-primary mb-1">{{ $itineraryItem['day'] }}</h3>
                            @if($itineraryItem['description'] !== '')
                                <p class="text-sm leading-7 text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ $itineraryItem['description'] }}</p>
                            @else
                                <p class="text-sm text-slate-500 dark:text-slate-400">Detail itinerary belum diisi.</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            </div>
        </section>
    @endif

    @if($relatedPackages->isNotEmpty())
        <section id="paket-lainnya" class="bg-white py-8 sm:py-10 dark:bg-background-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-primary">Rekomendasi</p>
                    <h2 class="mt-1 text-2xl font-black text-slate-950 dark:text-white">{{ $relatedSectionTitle }}</h2>
                </div>
                <a href="{{ route('packages.index', ['type' => $package->type]) }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-primary hover:text-primary dark:border-slate-700 dark:text-slate-200">
                    Lihat Semua
                    <x-fa-icon name="arrow-right" class="fa-fw text-xs" />
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($relatedPackages as $relatedPackage)
                    @php
                        $relatedImage = $relatedPackage->image_path ?: '/stitch_img_bus_shd.jpg';
                        $relatedThumb = media_thumbnail_url($relatedImage, 640, 75) ?? $relatedImage;
                    @endphp
                    <a href="{{ route('packages.show', $relatedPackage) }}" class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-primary/70 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900/50">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img src="{{ $relatedThumb }}" alt="{{ $relatedPackage->title }}" width="640" height="400" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                        </div>
                        <div class="p-3">
                            <h3 class="text-xs sm:text-sm font-black leading-snug text-slate-900 dark:text-slate-100 line-clamp-2">{{ $relatedPackage->title }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
            </div>
        </section>
    @endif
</main>

@include('partials.public.footer')
</body>
</html>
