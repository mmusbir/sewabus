@php
    $seoTitle = $seoTitle ?? setting('seo_meta_title_default', setting('site_name', 'Cahaya Bone'));
    $seoDescription = $seoDescription ?? setting('seo_meta_description_default', 'Layanan sewa bus pariwisata terpercaya dengan armada lengkap dan harga kompetitif.');
    $seoKeywords = $seoKeywords ?? setting('seo_meta_keywords_default', 'sewa bus pariwisata, rental bus, bus wisata');
    $seoImage = $seoImage ?? setting('seo_og_image', setting('hero_image_1', setting('hero_image', '/stitch_img_hero.jpg')));
    $seoCanonical = $seoCanonical ?? url()->current();
@endphp

<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
