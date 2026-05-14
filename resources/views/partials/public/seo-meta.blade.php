@php
    $siteName = setting('site_name', 'Sewa Bus Sulawesi Selatan');
    $seoTitle = $seoTitle ?? setting('seo_meta_title_default', $siteName . ' | Sewa Bus Semua Kabupaten Sulawesi Selatan');
    $seoDescription = $seoDescription ?? setting(
        'seo_meta_description_default',
        'Layanan sewa bus pariwisata di semua kabupaten/kota Sulawesi Selatan: Makassar, Bone, Maros, Gowa, hingga Toraja. Armada lengkap, harga transparan, driver profesional.'
    );
    $seoKeywords = $seoKeywords ?? setting(
        'seo_meta_keywords_default',
        'sewa bus sulawesi selatan, sewa bus makassar, rental bus bone, sewa bus maros, sewa bus gowa, sewa bus toraja, bus pariwisata sulawesi selatan'
    );
    $seoImage = $seoImage ?? setting('seo_og_image', setting('hero_image_1', setting('hero_image', '/stitch_img_hero.jpg')));
    $seoCanonical = $seoCanonical ?? url()->current();
    $seoImageAbsolute = is_string($seoImage) ? url($seoImage) : $seoImage;
    $seoKeywordsList = collect(explode(',', (string) $seoKeywords))
        ->map(fn (string $keyword) => trim($keyword))
        ->filter()
        ->unique()
        ->values()
        ->all();
    $serviceAreas = south_sulawesi_service_areas();
    $socialProfiles = collect([
        setting('social_facebook_url'),
        setting('social_instagram_url'),
        setting('social_tiktok_url'),
    ])
        ->filter(fn ($url) => is_string($url) && trim($url) !== '' && trim($url) !== '#')
        ->values()
        ->all();

    $travelAgencySchema = [
        '@context' => 'https://schema.org',
        '@type' => 'TravelAgency',
        'name' => $siteName,
        'url' => url('/'),
        'description' => $seoDescription,
        'telephone' => setting('contact_phone', ''),
        'email' => setting('contact_email', ''),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => setting('contact_address', 'Sulawesi Selatan, Indonesia'),
            'addressRegion' => 'Sulawesi Selatan',
            'addressCountry' => 'ID',
        ],
        'areaServed' => array_map(fn (string $area) => [
            '@type' => 'AdministrativeArea',
            'name' => $area,
        ], $serviceAreas),
        'serviceType' => [
            'Sewa bus pariwisata',
            'Sewa bus rombongan',
            'Sewa bus wisata sekolah',
            'Sewa bus perjalanan perusahaan',
        ],
    ];

    if ($socialProfiles !== []) {
        $travelAgencySchema['sameAs'] = $socialProfiles;
    }

    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Sewa Bus Pariwisata Sulawesi Selatan',
        'provider' => [
            '@type' => 'TravelAgency',
            'name' => $siteName,
            'url' => url('/'),
        ],
        'description' => $seoDescription,
        'url' => $seoCanonical,
        'areaServed' => array_map(fn (string $area) => [
            '@type' => 'AdministrativeArea',
            'name' => $area,
        ], $serviceAreas),
        'keywords' => implode(', ', $seoKeywordsList),
    ];
@endphp

<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ implode(', ', $seoKeywordsList) }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta name="author" content="{{ $siteName }}">
<meta name="language" content="id">
<meta name="geo.region" content="ID-SN">
<meta name="geo.placename" content="Sulawesi Selatan">
<link rel="canonical" href="{{ $seoCanonical }}">
<link rel="alternate" hreflang="id-ID" href="{{ $seoCanonical }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="id_ID">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImageAbsolute }}">
<meta property="og:image:alt" content="Layanan sewa bus di semua kabupaten Sulawesi Selatan">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImageAbsolute }}">
<script type="application/ld+json">{!! json_encode($travelAgencySchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
