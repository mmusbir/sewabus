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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.public.plus-jakarta-fonts')
    @include('partials.fontawesome')
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
    $videoItem = $gallery->video?->media_path;
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $whatsappLink = $whatsappNumber
        ? "https://wa.me/{$whatsappNumber}?text=" . urlencode("Halo Admin, saya tertarik dengan {$gallery->title}. Bisa dibantu detail dan harga?")
        : null;
    $southSulawesiAreas = south_sulawesi_service_areas();
@endphp

<main x-data="{ previewOpen: false, previewImage: null }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
            <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
            Kembali ke Katalog
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
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
                <button type="button" class="w-full h-full bg-cover bg-center cursor-zoom-in" style="background-image: url('{{ $coverImage }}')" @click="previewOpen = true; previewImage = '{{ $coverImage }}'" aria-label="Preview gambar utama armada"></button>
            </div>
            @if($thumbnailItems->count() > 1)
                <div class="grid grid-cols-3 gap-2 p-3 border-t border-slate-200 dark:border-slate-800">
                    @foreach($thumbnailItems->take(6) as $imageItem)
                        <button type="button" class="aspect-[4/3] rounded-lg border border-slate-200 dark:border-slate-700 bg-cover bg-center cursor-zoom-in" style="background-image: url('{{ $imageItem }}')" @click="previewOpen = true; previewImage = '{{ $imageItem }}'" aria-label="Preview gambar armada"></button>
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
                        <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-sm" />
                        Pesan via WhatsApp
                    </a>
                @endif
                <a href="{{ route('katalog.index') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-lg font-bold hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2">
                    Lihat Armada Lainnya
                    <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                </a>
            </div>
            <div class="mt-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 p-4">
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-6">
                    Armada ini siap melayani perjalanan di semua kabupaten/kota Sulawesi Selatan, termasuk
                    {{ implode(', ', array_slice($southSulawesiAreas, 0, 8)) }}, dan wilayah lainnya sesuai kebutuhan rute Anda.
                </p>
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="previewOpen"
            x-transition.opacity
            class="fixed inset-0 z-[10000] bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="previewOpen = false"
            @keydown.escape.window="previewOpen = false"
        >
            <button type="button" class="absolute top-4 right-4 rounded-full bg-white/20 hover:bg-white/30 text-white p-2" @click="previewOpen = false" aria-label="Tutup preview gambar">
                <x-fa-icon name="xmark" class="fa-fw" />
            </button>
            <img :src="previewImage" alt="Preview Armada" class="max-h-[90vh] max-w-[92vw] rounded-xl border border-white/20 shadow-2xl object-contain">
        </div>
    </template>
</main>

@include('partials.public.footer')
</body>
</html>
