<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@php
    $seoTitle = setting('seo_katalog_title', setting('seo_meta_title_default', setting('site_name', 'Cahaya Bone | Bus Parawisata')) . ' - Katalog');
    $seoDescription = setting('seo_katalog_description', setting('seo_meta_description_default', 'Lihat katalog armada bus pariwisata lengkap untuk berbagai kebutuhan perjalanan.'));
    $seoCanonical = route('katalog.index');
@endphp
<title>{{ $seoTitle }}</title>
@include('partials.public.seo-meta', ['seoTitle' => $seoTitle, 'seoDescription' => $seoDescription, 'seoCanonical' => $seoCanonical])
<link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
@include('partials.fontawesome')
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
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
@if(!empty($databaseUnavailable))
<div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
    Katalog sementara tidak dapat mengambil data terbaru karena koneksi database sedang bermasalah. Halaman tetap ditampilkan dengan data kosong.
</div>
@endif
<div class="mb-8">
<h2 class="text-4xl font-black text-slate-900 dark:text-slate-100 mb-2">Katalog Semua Armada</h2>
<p class="text-slate-600 dark:text-slate-400 max-w-2xl">Temukan pilihan armada bus pariwisata terbaik dengan berbagai kapasitas dan fasilitas untuk menunjang kenyamanan perjalanan Anda.</p>
</div>
<div class="flex flex-col lg:flex-row gap-8">
<aside class="w-full lg:w-72 flex-shrink-0">
    <form method="GET" action="{{ route('katalog.index') }}" class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 sticky top-24 space-y-6">
        @if($searchTerm !== '')
            <input type="hidden" name="q" value="{{ $searchTerm }}">
        @endif
        @if($selectedCategory !== 'all')
            <input type="hidden" name="category" value="{{ $selectedCategory }}">
        @endif

        <div>
            <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                <x-fa-icon name="filter" class="fa-fw text-primary" /> Filter
            </h3>
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">Fasilitas</p>
            <div class="space-y-2">
                @foreach($facilityOptions as $facilityKey => $facilityConfig)
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                        <input class="rounded border-slate-300 text-primary focus:ring-primary" type="checkbox" name="facilities[]" value="{{ $facilityKey }}" {{ in_array($facilityKey, $selectedFacilities, true) ? 'checked' : '' }}/>
                        <span>{{ $facilityConfig['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">Kapasitas Kursi</p>
            <div class="space-y-2">
                @foreach($seatOptions as $seatKey => $seatConfig)
                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300 cursor-pointer">
                        <input class="text-primary focus:ring-primary" name="seats" type="radio" value="{{ $seatKey }}" {{ $selectedSeats === $seatKey ? 'checked' : '' }}/>
                        <span>{{ $seatConfig['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="flex-1 bg-primary text-white font-bold py-2.5 rounded-lg hover:bg-primary/90 transition-colors">
                Terapkan
            </button>
            <a href="{{ route('katalog.index') }}" class="flex-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold py-2.5 rounded-lg text-center hover:border-primary hover:text-primary transition-colors">
                Reset
            </a>
        </div>
    </form>
</aside>

<div class="flex-1">
    @php
        $tabBaseQuery = request()->except(['category', 'page']);
    @endphp
    <div class="flex border-b border-slate-200 dark:border-slate-800 mb-8 overflow-x-auto no-scrollbar">
        @foreach($categoryTabs as $categoryKey => $categoryLabel)
            @php
                $tabQuery = $tabBaseQuery;
                if ($categoryKey !== 'all') {
                    $tabQuery['category'] = $categoryKey;
                }
            @endphp
            <a href="{{ route('katalog.index', $tabQuery) }}" @class([
                'px-6 py-3 text-sm whitespace-nowrap border-b-4 transition-colors',
                'font-bold text-primary border-primary' => $selectedCategory === $categoryKey,
                'font-medium text-slate-500 dark:text-slate-400 border-transparent hover:text-primary' => $selectedCategory !== $categoryKey,
            ])>
                {{ $categoryLabel }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($galleries as $gallery)
            @include('partials.public.armada-card', ['gallery' => $gallery])
        @empty
            <div class="col-span-full text-center py-12 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-slate-500 dark:text-slate-400 mb-3">Tidak ada armada yang cocok dengan filter saat ini.</p>
                <a href="{{ route('katalog.index') }}" class="text-primary font-bold hover:underline">Tampilkan Semua Armada</a>
            </div>
        @endforelse
    </div>

    @if($galleries->hasPages())
        <div class="mt-10">
            {{ $galleries->onEachSide(1)->links() }}
        </div>
    @endif
</div>
</div>
<section class="mt-10">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 lg:p-6">
        <h3 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-3">Layanan Sewa Bus untuk Seluruh Sulawesi Selatan & Sulawesi Barat</h3>
        <p class="text-sm text-slate-600 dark:text-slate-400 leading-6 mb-4">
            Cahaya Bone melayani kebutuhan sewa bus pariwisata untuk rombongan wisata, perusahaan, sekolah, keluarga, dan komunitas dengan cakupan rute dalam kota maupun antar kabupaten/kota.
        </p>
        <p class="text-sm text-slate-600 dark:text-slate-400 leading-6">
            Area layanan kami mencakup Makassar, Parepare, Palopo, Bantaeng, Barru, Bone, Bulukumba, Enrekang, Gowa, Jeneponto, Kepulauan Selayar, Luwu, Luwu Timur, Luwu Utara, Maros, Pangkep, Pinrang, Sidrap, Sinjai, Soppeng, Takalar, Tana Toraja, Toraja Utara, Wajo, serta wilayah Sulawesi Barat seperti Mamuju, Mamuju Tengah, Pasangkayu, Majene, Polewali Mandar, dan Mamasa.
        </p>
    </div>
</section>
</main>
@include('partials.public.footer')
</body>
</html>
