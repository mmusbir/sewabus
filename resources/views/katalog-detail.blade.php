<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @php
        $seoTitle = ($gallery->title . ' - ') . setting('seo_katalog_title', setting('seo_meta_title_default', setting('site_name', 'Sewa Bus Sulawesi Selatan')));
        $seoDescription = $gallery->description ?: setting('seo_katalog_description', setting('seo_meta_description_default', 'Detail armada sewa bus untuk semua kabupaten/kota Sulawesi Selatan.'));
        $seoKeywords = setting('seo_meta_keywords_default', 'detail armada sewa bus sulawesi selatan, rental bus makassar, sewa bus rombongan sulsel');
        $seoImage = $gallery->image_path ?: setting('seo_og_image', setting('hero_image_1', setting('hero_image', '/stitch_img_hero.jpg')));
        $seoCanonical = route('katalog.show', $gallery);
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
@include('partials.public.header', ['variant' => 'katalog', 'sticky' => false])

@php
    $facilityItems = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $gallery->facilities ?? '')));
    $imageItems = $gallery->images->pluck('media_path')->filter()->values();
    $coverImage = $gallery->image_path ?: ($imageItems->first() ?? '/stitch_img_bus_shd.jpg');
    $thumbnailItems = $imageItems
        ->reject(fn ($imageItem) => $imageItem === $coverImage)
        ->prepend($coverImage)
        ->filter()
        ->unique()
        ->values();
    $galleryPreviewItems = $thumbnailItems
        ->take(6)
        ->values()
        ->map(fn ($imageItem, $imageIndex) => [
            'src' => $imageItem,
            'title' => $gallery->title,
            'index' => $imageIndex,
        ])
        ->all();
    $videoItem = $gallery->video?->media_path;
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $whatsappLink = $whatsappNumber
        ? "https://wa.me/{$whatsappNumber}?text=" . urlencode("Halo Admin, saya tertarik dengan {$gallery->title}. Bisa dibantu detail dan harga?")
        : null;
    $southSulawesiAreas = south_sulawesi_service_areas();
    $highlightedServiceAreas = array_slice($southSulawesiAreas, 0, 4);
    $remainingServiceAreas = array_slice($southSulawesiAreas, 4);
@endphp

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
            <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
            Kembali ke Katalog
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden" data-armada-gallery data-armada-images='@json($galleryPreviewItems)'>
            <div class="relative aspect-[16/10]">
                <div class="absolute top-4 inset-x-4 z-10 flex items-start justify-between gap-2">
                    <span @class([
                        'text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider',
                        gallery_category_badge_class($gallery->category),
                    ])>
                        {{ gallery_category_label($gallery->category, $gallery->category) }}
                    </span>
                    @if(filled($gallery->po_key))
                        <span
                            class="text-[10px] font-bold px-3 py-1 rounded-full tracking-wide shadow-lg shadow-slate-950/10"
                            style="{{ gallery_po_badge_style($gallery->po_key) }}"
                        >
                            PO {{ gallery_po_label($gallery->po_key, $gallery->po_key) }}
                        </span>
                    @endif
                </div>
                <button
                    type="button"
                    class="group relative h-full w-full cursor-zoom-in overflow-hidden"
                    data-preview-open
                    data-preview-src="{{ $coverImage }}"
                    data-preview-title="{{ $gallery->title }}"
                    data-preview-index="0"
                    aria-label="Preview gambar utama armada"
                >
                    <img
                        data-gallery-main-image
                        src="{{ $coverImage }}"
                        alt="{{ $gallery->title }}"
                        width="960"
                        height="600"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="eager"
                        fetchpriority="high"
                        decoding="async"
                    >
                    <span class="absolute bottom-4 left-4 rounded-full bg-black/55 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md">
                        Ketuk untuk lihat besar
                    </span>
                </button>
            </div>
            @if($thumbnailItems->count() > 1)
                <div class="grid grid-cols-3 gap-2 p-3 border-t border-slate-200 dark:border-slate-800">
                    @foreach($thumbnailItems->take(6) as $imageIndex => $imageItem)
                        <button
                            type="button"
                            @class([
                                'group aspect-[4/3] overflow-hidden rounded-lg border bg-slate-100 transition dark:bg-slate-800',
                                'cursor-pointer hover:border-primary' => $imageIndex !== 0,
                                'cursor-pointer border-primary ring-2 ring-primary/25 shadow-[0_0_0_1px_rgba(225,106,55,0.08)]' => $imageIndex === 0,
                                'border-slate-200 dark:border-slate-700' => $imageIndex !== 0,
                            ])
                            data-gallery-thumb
                            data-gallery-src="{{ $imageItem }}"
                            data-gallery-title="{{ $gallery->title }}"
                            data-preview-index="{{ $imageIndex }}"
                            aria-pressed="{{ $imageIndex === 0 ? 'true' : 'false' }}"
                            aria-label="Preview gambar armada {{ $imageIndex + 1 }}"
                        >
                            <img
                                src="{{ $imageItem }}"
                                alt="{{ $gallery->title }} - gambar {{ $imageIndex + 1 }}"
                                width="320"
                                height="240"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy"
                                decoding="async"
                            >
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-3xl lg:text-4xl font-black mb-4">{{ $gallery->title }}</h1>
            @if(filled($gallery->po_key))
                <div class="mb-4">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide"
                        style="{{ gallery_po_badge_style($gallery->po_key) }}"
                    >
                        PO {{ gallery_po_label($gallery->po_key, $gallery->po_key) }}
                    </span>
                </div>
            @endif
            <p class="text-slate-600 dark:text-slate-300 text-base mb-6 whitespace-pre-line">{{ $gallery->description ?? 'Deskripsi lengkap belum tersedia.' }}</p>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <x-fa-icon name="list-check" class="fa-fw text-primary" />
                    Fasilitas Armada
                </h2>
                @if(count($facilityItems))
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-700 dark:text-slate-300">
                        @foreach($facilityItems as $facility)
                            <li class="flex items-start gap-2">
                                <x-fa-icon name="circle-check" class="fa-fw text-primary text-sm mt-0.5" />
                                <span>{{ $facility }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Fasilitas belum ditambahkan untuk armada ini.</p>
                @endif
            </div>

            @if($videoItem)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                        <x-fa-icon name="circle-play" class="fa-fw text-primary" />
                        Video Armada
                    </h2>
                    <video controls class="w-full rounded-xl bg-black">
                        <source src="{{ $videoItem }}">
                    </video>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4">
                @if($whatsappLink)
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                        <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-base" />
                        Pesan via WhatsApp
                    </a>
                @endif
                <a href="{{ route('katalog.index') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-lg font-bold hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2">
                    Lihat Armada Lainnya
                    <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                </a>
            </div>
            <div class="mt-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 p-4">
                <div class="sm:hidden">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-2">Area Layanan</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($highlightedServiceAreas as $serviceArea)
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                {{ $serviceArea }}
                            </span>
                        @endforeach
                    </div>
                    @if(count($remainingServiceAreas))
                        <details class="mt-2 group">
                            <summary class="cursor-pointer list-none text-xs font-semibold text-primary">Lihat semua area</summary>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($remainingServiceAreas as $serviceArea)
                                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                        {{ $serviceArea }}
                                    </span>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
                <p class="hidden sm:block text-sm text-slate-600 dark:text-slate-300 leading-6">
                    Armada ini siap melayani perjalanan di semua kabupaten/kota Sulawesi Selatan, termasuk
                    {{ implode(', ', array_slice($southSulawesiAreas, 0, 8)) }}, dan wilayah lainnya sesuai kebutuhan rute Anda.
                </p>
            </div>
        </div>
    </div>

    <div data-preview-modal class="fixed inset-0 z-[10000] hidden h-[100dvh] items-center justify-center bg-slate-950/95 p-3 backdrop-blur-md sm:p-6">
        <div class="flex h-full w-full max-w-6xl flex-col">
            <div class="mb-3 flex items-center justify-between gap-3 text-white">
                <div class="min-w-0">
                    <p data-preview-caption class="truncate text-sm font-bold sm:text-base">{{ $gallery->title }}</p>
                    <p data-preview-count class="text-xs text-white/60">1 / {{ max(1, count($galleryPreviewItems)) }}</p>
                </div>
                <button type="button" data-preview-close class="inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/15 transition hover:bg-white/20" aria-label="Tutup preview gambar">
                    <x-fa-icon name="xmark" class="fa-fw text-lg" />
                </button>
            </div>
            <div class="relative min-h-0 flex-1 overflow-hidden rounded-2xl border border-white/10 bg-black shadow-2xl">
                <img data-preview-image src="" alt="Preview Armada" class="h-full w-full object-contain">
                <button type="button" data-preview-prev class="absolute left-3 top-1/2 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/45 text-white ring-1 ring-white/15 backdrop-blur-md transition hover:bg-black/65 sm:left-5 sm:size-12" aria-label="Gambar sebelumnya">
                    <x-fa-icon name="chevron-left" class="fa-fw text-xl" />
                </button>
                <button type="button" data-preview-next class="absolute right-3 top-1/2 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/45 text-white ring-1 ring-white/15 backdrop-blur-md transition hover:bg-black/65 sm:right-5 sm:size-12" aria-label="Gambar berikutnya">
                    <x-fa-icon name="chevron-right" class="fa-fw text-xl" />
                </button>
            </div>
            <p class="mt-3 text-center text-xs text-white/55 sm:hidden">Geser kiri/kanan untuk pindah gambar</p>
        </div>
    </div>
</main>

@include('partials.public.footer')
</body>
</html>
