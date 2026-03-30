<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @php
        $seoTitle = ($gallery->title . ' - ') . setting('seo_katalog_title', setting('seo_meta_title_default', setting('site_name', 'Cahaya Bone | Bus Parawisata')));
        $seoDescription = $gallery->description ?: setting('seo_katalog_description', setting('seo_meta_description_default', 'Detail armada bus pariwisata.'));
        $seoImage = $gallery->image_path ?: setting('seo_og_image', setting('hero_image_1', setting('hero_image', '/stitch_img_hero.jpg')));
        $seoCanonical = route('katalog.show', $gallery);
    @endphp
    <title>{{ $seoTitle }}</title>
    @include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoCanonical' => $seoCanonical, 'seoImage' => $seoImage])
    <link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <style>
        :root {
            --color-primary: 225 106 55;
            --color-secondary-green: 1 128 61;
            --color-background-light: 248 246 246;
            --color-background-dark: 33 22 17;
            --font-display: "Space Grotesk";
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen">
@include('partials.public.header', ['variant' => 'katalog', 'sticky' => false])

@php
    $facilityItems = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $gallery->facilities ?? '')));
    $imageItems = $gallery->images->pluck('media_path')->filter()->values();
    $coverImage = $imageItems->first() ?? $gallery->image_path ?? '/stitch_img_bus_shd.jpg';
    $videoItem = $gallery->video?->media_path;
    $categoryLabels = [
        'minibus' => 'Minibus',
        'mediumbus' => 'Mediumbus',
        'bigbus' => 'Bigbus',
    ];
    $categoryBadgeClasses = [
        'minibus' => 'bg-emerald-600',
        'mediumbus' => 'bg-amber-500',
        'bigbus' => 'bg-rose-600',
    ];
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $whatsappLink = $whatsappNumber
        ? "https://wa.me/{$whatsappNumber}?text=" . urlencode("Halo Admin, saya tertarik dengan {$gallery->title}. Bisa dibantu detail dan harga?")
        : null;
@endphp

<main x-data="{ previewOpen: false, previewImage: null }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('katalog.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Katalog
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="relative aspect-[16/10]">
                <div class="absolute top-4 left-4 z-10">
                    <span @class([
                        'text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider',
                        $categoryBadgeClasses[$gallery->category] ?? 'bg-secondary-green',
                    ])>
                        {{ $categoryLabels[$gallery->category] ?? $gallery->category }}
                    </span>
                </div>
                <button type="button" class="w-full h-full bg-cover bg-center cursor-zoom-in" style="background-image: url('{{ $coverImage }}')" @click="previewOpen = true; previewImage = '{{ $coverImage }}'" aria-label="Preview gambar utama armada"></button>
            </div>
            @if($imageItems->count() > 1)
                <div class="grid grid-cols-3 gap-2 p-3 border-t border-slate-200 dark:border-slate-800">
                    @foreach($imageItems->take(6) as $imageItem)
                        <button type="button" class="aspect-[4/3] rounded-lg border border-slate-200 dark:border-slate-700 bg-cover bg-center cursor-zoom-in" style="background-image: url('{{ $imageItem }}')" @click="previewOpen = true; previewImage = '{{ $imageItem }}'" aria-label="Preview gambar armada"></button>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h1 class="text-3xl lg:text-4xl font-black mb-4">{{ $gallery->title }}</h1>
            <p class="text-slate-600 dark:text-slate-300 text-base mb-6 whitespace-pre-line">{{ $gallery->description ?? 'Deskripsi lengkap belum tersedia.' }}</p>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 mb-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">checklist</span>
                    Fasilitas Armada
                </h2>
                @if(count($facilityItems))
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-slate-700 dark:text-slate-300">
                        @foreach($facilityItems as $facility)
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
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
                        <span class="material-symbols-outlined text-primary">play_circle</span>
                        Video Armada
                    </h2>
                    <video controls class="w-full rounded-xl bg-black">
                        <source src="{{ $videoItem }}">
                    </video>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-4">
                @if($whatsappLink)
                    <a href="{{ $whatsappLink }}" target="_blank" class="bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">chat</span>
                        Pesan via WhatsApp
                    </a>
                @endif
                <a href="{{ route('katalog.index') }}" class="bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-lg font-bold hover:border-primary hover:text-primary transition-colors flex items-center justify-center gap-2">
                    Lihat Armada Lainnya
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
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
                <span class="material-symbols-outlined">close</span>
            </button>
            <img :src="previewImage" alt="Preview Armada" class="max-h-[90vh] max-w-[92vw] rounded-xl border border-white/20 shadow-2xl object-contain">
        </div>
    </template>
</main>

@include('partials.public.footer')
</body>
</html>
