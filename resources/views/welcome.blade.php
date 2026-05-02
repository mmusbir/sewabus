<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    @php
        $seoTitle = setting('seo_home_title', setting('seo_meta_title_default', setting('site_name', 'Multibus | Bus Parawisata')));
        $seoDescription = setting('seo_home_description', setting('seo_meta_description_default', 'Layanan sewa bus pariwisata terpercaya dengan armada lengkap dan harga kompetitif.'));
        $seoCanonical = route('home');
    @endphp
    <title>{{ $seoTitle }}</title>
    @include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoCanonical' => $seoCanonical])
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
    @endphp
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
        @include('partials.public.header', ['variant' => 'home'])
        <main class="flex-1">
            @if(!empty($databaseUnavailable))
                <section class="px-6 lg:px-20 pt-6">
                    <div
                        class="max-w-7xl mx-auto rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Data armada sementara tidak dapat dimuat karena koneksi database sedang bermasalah. Konten utama
                        tetap tersedia dan Anda bisa mencoba lagi beberapa saat lagi.
                    </div>
                </section>
            @endif
            <section class="relative px-6 lg:px-20 py-10">
                <div class="max-w-7xl mx-auto">
                    <div x-data="{ current: 0, total: {{ count($heroImages) }}, interval: {{ $heroCarouselIntervalMs }}, start() { if (this.total <= 1) return; setInterval(() => { this.current = (this.current + 1) % this.total }, this.interval); } }"
                        x-init="start()"
                        class="relative overflow-hidden rounded-xl bg-slate-900 min-h-[500px] flex flex-col items-center justify-center text-center p-8 lg:p-16">
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
                                <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight mb-6">
                                    {{ setting('hero_title', 'Sewa Bus Pariwisata Terbaik untuk Perjalanan Anda') }}
                                </h1>
                            @endif
                            @if(setting('hero_show_subtitle', true))
                                <p class="text-lg text-slate-200 mb-10 leading-relaxed">
                                    {{ setting('hero_subtitle', 'Armada modern, fasilitas lengkap, dan pengemudi profesional siap menemani perjalanan wisata keluarga atau korporat Anda ke seluruh Indonesia.') }}
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
            <section class="px-6 lg:px-20 py-16 bg-white dark:bg-background-dark">
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
            <section class="px-6 lg:px-20 py-20 bg-background-light dark:bg-background-dark/50">
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
            <section class="px-6 lg:px-20 py-16">
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
                    <div class="relative z-10 flex flex-col sm:flex-row gap-3">
                        @if($whatsappCtaLink)
                            <a href="{{ $whatsappCtaLink }}" target="_blank" rel="noopener"
                                class="bg-white text-primary px-6 py-3 rounded-lg font-bold text-base hover:bg-slate-100 transition-colors inline-flex items-center justify-center gap-2 min-w-[220px]">
                                <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-[20px]" /> Hubungi via WhatsApp
                            </a>
                        @else
                            <button type="button" disabled
                                class="bg-white/70 text-primary/70 px-6 py-3 rounded-lg font-bold text-base inline-flex items-center justify-center gap-2 min-w-[220px] cursor-not-allowed">
                                <x-fa-icon name="whatsapp" style="brands" class="fa-fw text-[20px]" /> Hubungi via WhatsApp
                            </button>
                        @endif
                        @if($phoneCtaLink)
                            <a href="{{ $phoneCtaLink }}"
                                class="bg-secondary text-white px-6 py-3 rounded-lg font-bold text-base hover:bg-secondary/90 transition-colors inline-flex items-center justify-center gap-2 min-w-[220px]">
                                <x-fa-icon name="phone" class="fa-fw text-[20px]" /> Telepon Langsung
                            </a>
                        @else
                            <button type="button" disabled
                                class="bg-secondary/70 text-white/70 px-6 py-3 rounded-lg font-bold text-base inline-flex items-center justify-center gap-2 min-w-[220px] cursor-not-allowed">
                                <x-fa-icon name="phone" class="fa-fw text-[20px]" /> Telepon Langsung
                            </button>
                        @endif
                    </div>
                </div>
            </section>
            <section class="px-6 lg:px-20 py-14 bg-background-light dark:bg-background-dark/50">
                <div class="max-w-7xl mx-auto">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 lg:p-8">
                        <h2 class="text-2xl lg:text-3xl font-black text-slate-900 dark:text-slate-100 mb-4">Area Layanan
                            Sewa Bus Sulawesi Selatan &amp; Sulawesi Barat</h2>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                            Multibus melayani sewa bus pariwisata untuk perjalanan wisata, ziarah, kunjungan sekolah,
                            acara kantor, dan antar-jemput rombongan di seluruh wilayah Sulawesi Selatan dan Sulawesi
                            Barat. Layanan tersedia untuk keberangkatan dalam kota, antar kabupaten/kota, hingga
                            perjalanan lintas provinsi sesuai kebutuhan Anda.
                        </p>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div
                                class="rounded-xl border border-slate-200 dark:border-slate-800 p-5 bg-slate-50 dark:bg-slate-800/40">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-3">Cakupan Sulawesi
                                    Selatan</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-6">
                                    Makassar, Parepare, Palopo, Bantaeng, Barru, Bone, Bulukumba, Enrekang, Gowa,
                                    Jeneponto, Kepulauan Selayar, Luwu, Luwu Timur, Luwu Utara, Maros, Pangkajene dan
                                    Kepulauan (Pangkep), Pinrang, Sidenreng Rappang (Sidrap), Sinjai, Soppeng, Takalar,
                                    Tana Toraja, Toraja Utara, dan Wajo.
                                </p>
                            </div>
                            <div
                                class="rounded-xl border border-slate-200 dark:border-slate-800 p-5 bg-slate-50 dark:bg-slate-800/40">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-3">Cakupan Sulawesi
                                    Barat</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-6">
                                    Mamuju, Mamuju Tengah, Pasangkayu, Majene, Polewali Mandar, dan Mamasa. Tim kami
                                    siap membantu rekomendasi rute, pilihan armada, serta estimasi biaya perjalanan
                                    untuk setiap daerah tujuan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        @include('partials.public.footer', ['variant' => 'home'])
    </div>
</body>

</html>