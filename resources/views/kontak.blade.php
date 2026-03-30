<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@php
    $seoTitle = setting('seo_contact_title', setting('seo_meta_title_default', setting('site_name', 'Cahaya Bone | Bus Parawisata')) . ' - Kontak');
    $seoDescription = setting('seo_contact_description', setting('seo_meta_description_default', 'Hubungi tim kami untuk konsultasi dan pemesanan bus pariwisata.'));
    $seoCanonical = route('contact.index');
@endphp
<title>{{ $seoTitle }}</title>
@include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoCanonical' => $seoCanonical])
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
@include('partials.public.header', ['sticky' => false])

@php
    $contactAddress = setting('contact_address', 'Jl. Pariwisata No. 123, Jakarta Selatan');
    $contactPhone = setting('contact_phone', '(021) 1234 5678');
    $contactEmail = setting('contact_email', 'info@buspariwisata.co.id');
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', '62812345678'));
    $whatsappHref = 'https://wa.me/' . $whatsappNumber;

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
        <h1 class="text-4xl font-black text-slate-900 dark:text-slate-100 mb-3">Hubungi Kami</h1>
        <p class="text-slate-600 dark:text-slate-400 max-w-3xl">Tim kami siap membantu konsultasi paket sewa bus, rute perjalanan, dan estimasi biaya sesuai kebutuhan Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">location_on</span>
            </div>
            <h2 class="font-bold text-lg mb-2">Alamat Kantor</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $contactAddress }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">call</span>
            </div>
            <h2 class="font-bold text-lg mb-2">Telepon</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $contactPhone }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="size-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">mail</span>
            </div>
            <h2 class="font-bold text-lg mb-2">Email</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ $contactEmail }}</p>
        </article>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <section class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h2 class="text-xl font-bold mb-4">Kontak Cepat</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Klik tombol berikut untuk langsung terhubung dengan admin via WhatsApp.</p>
            <a href="{{ $whatsappHref }}" target="_blank" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-lg font-bold hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-sm">chat</span>
                Chat via WhatsApp
            </a>
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
</main>

@include('partials.public.footer')
</body>
</html>
