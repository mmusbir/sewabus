<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@php
    $seoTitle = setting('seo_contact_title', setting('seo_meta_title_default', setting('site_name', 'Sewa Bus Sulawesi Selatan')) . ' - Kontak Sewa Bus');
    $seoDescription = setting('seo_contact_description', setting('seo_meta_description_default', 'Hubungi tim sewa bus untuk semua kabupaten/kota Sulawesi Selatan. Konsultasi rute, pilihan armada, dan estimasi harga perjalanan rombongan.'));
    $seoKeywords = setting('seo_meta_keywords_default', 'kontak sewa bus sulawesi selatan, wa sewa bus makassar, sewa bus semua kabupaten sulsel');
    $seoCanonical = route('contact.index');
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

@php
    $contactAddress = setting('contact_address', 'Sulawesi Selatan, Indonesia');
    $contactPhone = setting('contact_phone', '(021) 1234 5678');
    $contactEmail = setting('contact_email', 'info@buspariwisata.co.id');
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', '62812345678'));
    $whatsappHref = $whatsappNumber ? 'https://wa.me/' . $whatsappNumber : null;
    $phoneHref = preg_replace('/[^0-9+]/', '', $contactPhone);
    $phoneHref = $phoneHref !== '' ? 'tel:' . $phoneHref : null;
    $emailHref = trim((string) $contactEmail) !== '' ? 'mailto:' . trim((string) $contactEmail) : null;
    $southSulawesiAreas = south_sulawesi_service_areas();

    $footerMapValue = setting('footer_map_url');
    $footerMapSrc = null;

    if (!empty($footerMapValue)) {
        if (preg_match('/^\s*-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?\s*$/', $footerMapValue)) {
            $footerMapSrc = 'https://www.google.com/maps?q=' . urlencode($footerMapValue) . '&output=embed';
        } elseif (str_contains($footerMapValue, 'output=embed') || str_contains($footerMapValue, '/maps/embed')) {
            $footerMapSrc = $footerMapValue;
        } elseif (preg_match('/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $footerMapValue, $matches)) {
            $footerMapSrc = 'https://www.google.com/maps?q=' . $matches[1] . ',' . $matches[2] . '&output=embed';
        } else {
            $footerMapSrc = $footerMapValue;
        }
    }
@endphp

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-10">
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-slate-100 mb-3">Hubungi Tim Sewa Bus Sulawesi Selatan</h1>
        <p class="text-slate-600 dark:text-slate-400 max-w-3xl">Tim kami siap membantu konsultasi paket sewa bus, rute perjalanan, dan estimasi biaya untuk semua kabupaten/kota di Sulawesi Selatan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <x-fa-icon name="location-dot" class="fa-fw" />
            </div>
            <h2 class="font-bold text-lg mb-2">Alamat Kantor</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $contactAddress }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                <x-fa-icon name="phone" class="fa-fw" />
            </div>
            <h2 class="font-bold text-lg mb-2">Telepon</h2>
            @if($phoneHref)
                <a href="{{ $phoneHref }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-primary">{{ $contactPhone }}</a>
            @else
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $contactPhone }}</p>
            @endif
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <x-fa-icon name="envelope" class="fa-fw" />
            </div>
            <h2 class="font-bold text-lg mb-2">Email</h2>
            @if($emailHref)
                <a href="{{ $emailHref }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-primary">{{ $contactEmail }}</a>
            @else
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $contactEmail }}</p>
            @endif
        </article>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <section class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-xl font-bold mb-4">Kontak Cepat</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Klik tombol berikut untuk langsung terhubung dengan admin via WhatsApp.</p>
            @if($whatsappHref)
                <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">
                    <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-sm" />
                    Chat via WhatsApp
                </a>
            @else
                <button type="button" disabled class="inline-flex items-center gap-2 bg-primary/70 text-white/70 px-6 py-3 rounded-lg font-bold cursor-not-allowed">
                    <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-sm" />
                    Nomor WhatsApp Belum Tersedia
                </button>
            @endif
        </section>
        <section class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-xl font-bold mb-4">Lokasi</h2>
            <div class="h-72 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden">
                @if($footerMapSrc)
                    <iframe class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $footerMapSrc }}" allowfullscreen></iframe>
                @elseif(setting('footer_map_image'))
                    <img class="w-full h-full object-cover" src="{{ setting('footer_map_image') }}" alt="Peta lokasi kantor"/>
                @else
                    <img class="w-full h-full object-cover" src="/stitch_img_map.jpg" alt="Peta lokasi kantor"/>
                @endif
            </div>
        </section>
    </div>

    <section class="mt-8 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 lg:p-6">
        <h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-3">Cakupan Layanan Semua Kabupaten/Kota Sulawesi Selatan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
            @foreach($southSulawesiAreas as $serviceArea)
                <span class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $serviceArea }}</span>
            @endforeach
        </div>
    </section>
</main>

@include('partials.public.footer')
</body>
</html>
