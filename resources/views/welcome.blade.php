<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    @php
        $seoTitle = setting('seo_home_title', setting('seo_meta_title_default', setting('site_name', 'Sewa Bus Sulawesi Selatan') . ' | Sewa Bus Semua Kabupaten Sulawesi Selatan'));
        $seoDescription = setting('seo_home_description', setting('seo_meta_description_default', 'Sewa bus pariwisata untuk semua kabupaten/kota di Sulawesi Selatan. Armada lengkap, driver profesional, harga transparan untuk wisata, sekolah, kantor, dan rombongan keluarga.'));
        $seoKeywords = setting('seo_meta_keywords_default', 'sewa bus sulawesi selatan, sewa bus makassar, rental bus bone, sewa bus maros, sewa bus gowa, sewa bus toraja');
        $seoCanonical = route('home');
    @endphp
    <title>{{ $seoTitle }}</title>
    @include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoKeywords' => $seoKeywords, 'seoCanonical' => $seoCanonical])
    <link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap"
        rel="stylesheet" />
    @include('partials.fontawesome')
    <style>
        :root {
            --color-primary: 225 106 55;
            --color-secondary: 1 128 61;
            --color-background-light: 248 246 246;
            --color-background-dark: 33 22 17;
            --font-display: "Plus Jakarta Sans";
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100">
    @php
        $heroImages = array_values(array_filter([
            setting('hero_image_1'),
            setting('hero_image_2'),
            setting('hero_image_3'),
        ]));

        if (empty($heroImages)) {
            $heroImages = [setting('hero_image', '/stitch_img_hero.jpg')];
        }

        $heroCarouselIntervalMs = max(1000, ((int) setting('hero_carousel_interval_seconds', 5)) * 1000);
        $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
        $phoneRaw = setting('contact_phone', setting('social_whatsapp_number', ''));
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneRaw ?? '');
        $whatsappCtaLink = $whatsappNumber
            ? 'https://wa.me/' . $whatsappNumber . '?text=' . urlencode('Halo Admin, saya ingin konsultasi sewa bus.')
            : null;
        $phoneCtaLink = $phoneNumber ? 'tel:' . $phoneNumber : null;
        $southSulawesiAreas = south_sulawesi_service_areas();
    @endphp
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        @include('partials.public.header', ['variant' => 'home'])
        <main class="flex-1">
            @if(!empty($databaseUnavailable))
                <section class="px-4 sm:px-6 lg:px-20 pt-6">
                    <div
                        class="max-w-7xl mx-auto rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Data armada dan paket sementara tidak dapat dimuat karena koneksi database sedang bermasalah. Konten utama
                        tetap tersedia dan Anda bisa mencoba lagi beberapa saat lagi.
                    </div>
                </section>
            @endif
            <section class="relative px-4 sm:px-6 lg:px-20 py-8 sm:py-10">
                <div class="max-w-7xl mx-auto">
                    <div x-data="{ current: 0, total: {{ count($heroImages) }}, interval: {{ $heroCarouselIntervalMs }}, start() { if (this.total <= 1) return; setInterval(() => { this.current = (this.current + 1) % this.total }, this.interval); } }"
                        x-init="start()"
                        class="relative overflow-hidden rounded-2xl bg-slate-900 min-h-[420px] sm:min-h-[500px] flex flex-col items-center justify-center text-center p-6 sm:p-8 lg:p-16">
                        @foreach($heroImages as $index => $heroImage)
                            <div x-cloak x-show="current === {{ $index }}" x-transition.opacity.duration.700ms
                                class="absolute inset-0 bg-cover bg-center opacity-60"
                                data-alt="Tampilan depan bus pariwisata mewah di jalan raya" @style(["background-image: url('" . $heroImage . "')"])></div>
                        @endforeach
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-background-dark via-background-dark/40 to-transparent">
                        </div>
                        <div class="relative z-10 max-w-3xl">
                            <span
                                class="inline-block px-4 py-1.5 mb-6 text-xs font-bold uppercase tracking-widest text-white bg-secondary rounded-full">
                                <span class="flex items-center gap-2">
                                    <x-fa-icon name="circle-check" class="fa-fw text-sm" />
                                    Layanan Terpercaya &amp; Aman
                                </span>
                            </span>
                            @if(setting('hero_show_title', true))
                                <h1 class="text-3xl sm:text-4xl lg:text-6xl font-black text-white leading-tight mb-5 sm:mb-6">
                                    {{ setting('hero_title', 'Sewa Bus Pariwisata di Semua Kabupaten Sulawesi Selatan') }}
                                </h1>
                            @endif
                            @if(setting('hero_show_subtitle', true))
                                <p class="text-base sm:text-lg text-slate-200 mb-8 sm:mb-10 leading-relaxed">
                                    {{ setting('hero_subtitle', 'Armada modern, fasilitas lengkap, dan pengemudi profesional siap menemani perjalanan wisata keluarga atau korporat Anda di seluruh Sulawesi Selatan.') }}
                                </p>
                            @endif
                            @if(count($heroImages) > 1)
                                <div class="mt-8 flex items-center justify-center gap-2">
                                    @foreach($heroImages as $index => $heroImage)
                                        <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/40 transition-all"
                                            :class="current === {{ $index }} ? 'bg-white w-7' : 'bg-white/40'"
                                            @click="current = {{ $index }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
            <section class="px-4 sm:px-6 lg:px-20 py-12 sm:py-16 bg-white dark:bg-background-dark">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                        <div class="max-w-2xl">
                            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 mb-4">Galeri Armada
                                Unggulan Kami</h2>
                            <p class="text-slate-600 dark:text-slate-400">Pilih armada yang sesuai dengan kebutuhan
                                kapasitas dan gaya perjalanan Anda. Semua armada kami dalam kondisi prima.</p>
                        </div>
                        <a class="text-primary font-bold flex items-center gap-2 hover:underline"
                            href="{{ route('katalog.index') }}">
                            Lihat Semua Armada <x-fa-icon name="arrow-right" class="fa-fw" />
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($galleries ?? [] as $gallery)
                            @include('partials.public.armada-card', ['gallery' => $gallery])
                        @empty
                            <div class="col-span-full text-center py-12">
                                <p class="text-slate-500 dark:text-slate-400">Belum ada armada yang ditambahkan ke galeri.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
            <section class="px-4 sm:px-6 lg:px-20 py-12 sm:py-16 bg-background-light dark:bg-background-dark/40">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                        <div class="max-w-2xl">
                            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 mb-4">Paket Liburan
                                Pilihan</h2>
                            <p class="text-slate-600 dark:text-slate-400">Lihat rekomendasi paket liburan dengan armada
                                nyaman yang siap disesuaikan dengan rute perjalanan rombongan Anda.</p>
                        </div>
                        <a class="text-primary font-bold flex items-center gap-2 hover:underline"
                            href="{{ route('packages.index', ['type' => 'liburan']) }}">
                            Lihat Semua Paket Liburan <x-fa-icon name="arrow-right" class="fa-fw" />
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @forelse($liburanPackages ?? [] as $package)
                            @php
                                $packageImage = $package->image_path ?: '/stitch_img_bus_shd.jpg';
                                $packageThumbnail = media_thumbnail_url($packageImage, 640, 75) ?? $packageImage;
                            @endphp
                            <article
                                class="group bg-white dark:bg-slate-900/60 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 hover:shadow-xl transition-all">
                                <div class="relative h-52 overflow-hidden">
                                    <span
                                        class="absolute top-3 left-3 z-10 bg-amber-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                        Paket Liburan
                                    </span>
                                    <img
                                        src="{{ $packageThumbnail }}"
                                        alt="{{ $package->title }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    >
                                </div>
                                <div class="p-5">
                                    <h3 class="text-lg font-bold mb-2 text-slate-900 dark:text-slate-100">{{ $package->title }}</h3>
                                    @if(filled($package->price_label))
                                        <p class="mb-3 inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary">
                                            <x-fa-icon name="wallet" class="fa-fw text-[11px]" />
                                            {{ $package->price_label }}
                                        </p>
                                    @endif
                                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">{{ $package->description ?: 'Deskripsi paket belum tersedia.' }}</p>
                                    <a href="{{ route('packages.index', ['type' => 'liburan']) }}"
                                        class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                                        Lihat Paket
                                        <x-fa-icon name="arrow-right" class="fa-fw text-sm" />
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full text-center py-12 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                                <p class="text-slate-500 dark:text-slate-400">Belum ada paket liburan aktif yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
            <section class="px-4 sm:px-6 lg:px-20 py-14 sm:py-20 bg-background-light dark:bg-background-dark/50">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-black text-slate-900 dark:text-slate-100 mb-4">Mengapa Memilih Layanan
                            Kami?</h2>
                        <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Kami memberikan jaminan
                            kenyamanan dan keamanan di setiap kilometer perjalanan Anda dengan standar layanan
                            internasional.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                                <x-fa-icon name="shield-halved" class="fa-fw text-3xl" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Armada Terawat</h3>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Mesin dan kebersihan kabin
                                selalu dipastikan dalam kondisi prima sebelum berangkat demi kenyamanan Anda.</p>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="size-14 bg-secondary/10 rounded-xl flex items-center justify-center text-secondary mb-6">
                                <x-fa-icon name="wallet" class="fa-fw text-3xl" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Harga Kompetitif</h3>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Penawaran harga terbaik dengan
                                transparansi biaya tanpa tambahan tersembunyi. Hemat dan berkualitas.</p>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                            <div
                                class="size-14 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6">
                                <x-fa-icon name="id-badge" class="fa-fw text-3xl" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Driver Profesional</h3>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Tim pengemudi yang ramah,
                                profesional, berlisensi resmi, dan memahami rute perjalanan dengan sangat baik.</p>
                        </div>
                    </div>
                </div>
            </section>
            <section class="px-4 sm:px-6 lg:px-20 py-12 sm:py-16">
                <div
                    class="max-w-7xl mx-auto bg-primary rounded-2xl p-10 lg:p-20 flex flex-col lg:flex-row items-center justify-between gap-10 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-10">
                        <x-fa-icon name="headset" class="fa-fw text-[200px]" />
                    </div>
                    <div class="relative z-10 max-w-xl">
                        <h2 class="text-3xl lg:text-5xl font-black mb-6">Siap Merencanakan Perjalanan Anda?</h2>
                        <p class="text-white/80 text-lg mb-0 leading-relaxed">Tim admin kami tersedia 24/7 untuk
                            membantu reservasi dan konsultasi rute perjalanan Anda agar lebih efisien.</p>
                    </div>
                    <div class="relative z-10 flex w-full flex-col sm:flex-row gap-3">
                        @if($whatsappCtaLink)
                            <a href="{{ $whatsappCtaLink }}" target="_blank" rel="noopener"
                                class="bg-white text-primary px-6 py-3 rounded-lg font-bold text-base hover:bg-slate-100 transition-colors inline-flex items-center justify-center gap-2 w-full sm:w-auto sm:min-w-[220px]">
                                <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-[20px]" /> Hubungi via WhatsApp
                            </a>
                        @else
                            <button type="button" disabled
                                class="bg-white/70 text-primary/70 px-6 py-3 rounded-lg font-bold text-base inline-flex items-center justify-center gap-2 w-full sm:w-auto sm:min-w-[220px] cursor-not-allowed">
                                <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-[20px]" /> Hubungi via WhatsApp
                            </button>
                        @endif
                        @if($phoneCtaLink)
                            <a href="{{ $phoneCtaLink }}"
                                class="bg-secondary text-white px-6 py-3 rounded-lg font-bold text-base hover:bg-secondary/90 transition-colors inline-flex items-center justify-center gap-2 w-full sm:w-auto sm:min-w-[220px]">
                                <x-fa-icon name="phone" class="fa-fw text-[20px]" /> Telepon Langsung
                            </a>
                        @else
                            <button type="button" disabled
                                class="bg-secondary/70 text-white/70 px-6 py-3 rounded-lg font-bold text-base inline-flex items-center justify-center gap-2 w-full sm:w-auto sm:min-w-[220px] cursor-not-allowed">
                                <x-fa-icon name="phone" class="fa-fw text-[20px]" /> Telepon Langsung
                            </button>
                        @endif
                    </div>
                </div>
            </section>
            <section class="px-4 sm:px-6 lg:px-20 py-12 sm:py-14 bg-background-light dark:bg-background-dark/50">
                <div class="max-w-7xl mx-auto">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 lg:p-8">
                        <h2 class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-slate-100 mb-4">
                            Area Layanan Sewa Bus Semua Kabupaten/Kota Sulawesi Selatan
                        </h2>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                            Layanan sewa bus kami tersedia untuk seluruh wilayah Sulawesi Selatan. Anda bisa memesan
                            bus wisata untuk keberangkatan dalam kota, antar kabupaten/kota, hingga perjalanan lintas
                            provinsi sesuai kebutuhan rombongan.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                            @foreach($southSulawesiAreas as $serviceArea)
                                <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $serviceArea }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-6">
                            Butuh rute luar Sulawesi Selatan? Tim kami siap bantu rencana perjalanan ke Sulawesi Barat
                            dan antar provinsi dengan armada serta estimasi biaya yang sesuai.
                        </p>
                    </div>
                </div>
            </section>
        </main>
        @include('partials.public.footer', ['variant' => 'home'])
    </div>
</body>

</html>
