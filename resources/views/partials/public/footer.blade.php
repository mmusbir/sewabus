@php
    $variant = $variant ?? 'home';
    $galleryCategories = gallery_category_list();
    $footerLogoImage = setting('footer_logo_image');
    $footerLogoImageOptimized = media_thumbnail_url($footerLogoImage, 320, 82) ?? $footerLogoImage;
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $whatsappHref = $whatsappNumber ? "https://wa.me/{$whatsappNumber}" : '#';
    $socialLinks = [
        [
            'label' => 'Facebook',
            'href' => setting('social_facebook_url', '#'),
            'icon' => 'facebook-f',
            'style' => 'brands',
        ],
        [
            'label' => 'Instagram',
            'href' => setting('social_instagram_url', '#'),
            'icon' => 'instagram',
            'style' => 'brands',
        ],
        [
            'label' => 'TikTok',
            'href' => setting('social_tiktok_url', '#'),
            'icon' => 'tiktok',
            'style' => 'brands',
        ],
        [
            'label' => 'WhatsApp',
            'href' => $whatsappHref,
            'icon' => 'whatsapp',
            'style' => 'brands',
        ],
    ];
@endphp

@if($variant === 'katalog')
    <footer class="bg-white border-t border-slate-200 mt-20 pt-14 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 mb-14">
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-2 mb-5">
                        @if($footerLogoImage)
                            <img src="{{ $footerLogoImageOptimized }}" alt="Logo Footer" width="160" height="40" class="h-10 object-contain">
                        @endif
                        @if(setting('footer_logo_show_text', true) && !$footerLogoImage)
                            <x-fa-icon name="bus" class="fa-fw text-primary text-3xl" />
                            <h2 class="text-lg font-bold tracking-tight text-slate-900">{{ setting('footer_logo_text', 'BusPariwisata') }}</h2>
                        @endif
                    </div>
                    <p class="text-slate-600 text-sm leading-6 max-w-xs">
                        {{ setting('footer_description', 'Layanan penyewaan bus pariwisata terpercaya di Indonesia dengan pilihan armada terlengkap dan pelayanan prima.') }}
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900 mb-4">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm text-slate-600 leading-6">
                        <li><a class="hover:text-primary transition-colors" href="{{ route('home') }}">Beranda</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index') }}">Armada</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('packages.index') }}">Paket Sewa</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('contact.index') }}">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900 mb-4">Kategori Bus</h4>
                    <ul class="space-y-3 text-sm text-slate-600 leading-6">
                        @foreach($galleryCategories as $category)
                            <li>
                                <a class="hover:text-primary transition-colors" href="{{ route('katalog.index', ['category' => $category['key']]) }}">
                                    {{ gallery_category_full_label($category['key']) }}
                                </a>
                            </li>
                        @endforeach
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index') }}">Semua Armada</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900 mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3 text-sm text-slate-600 leading-6">
                        <li class="flex items-start gap-3">
                            <x-fa-icon name="location-dot" class="fa-fw text-primary text-sm mt-1" />
                            <span>{{ setting('contact_address', 'Sulawesi Selatan, Indonesia') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fa-icon name="phone" class="fa-fw text-primary text-sm" />
                            <span>{{ setting('contact_phone', '(021) 1234 5678') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-fa-icon name="envelope" class="fa-fw text-primary text-sm" />
                            <span>{{ setting('contact_email', 'info@buspariwisata.co.id') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-500">&copy; 2026 Multibus - Sewa Bus Pariwisata. All Rights Reserved.</p>
                <div class="flex items-center gap-4">
                    @foreach($socialLinks as $social)
                        <a class="text-slate-400 hover:text-primary transition-colors" href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" {{ $social['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                            <x-fa-icon :name="$social['icon']" :style="$social['style']" class="text-lg" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
@else
    <footer class="bg-slate-900 text-slate-300 px-6 lg:px-20 py-14">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
            <div class="col-span-1 lg:col-span-1">
                <div class="flex items-center gap-3 mb-5">
                    @if($footerLogoImage)
                        <img src="{{ $footerLogoImageOptimized }}" alt="Logo Footer" width="160" height="40" class="h-10 object-contain filter brightness-0 invert">
                    @endif
                    @if(setting('footer_logo_show_text', true) && !$footerLogoImage)
                        <x-fa-icon name="bus" class="fa-fw text-3xl text-primary" />
                        <h2 class="text-lg font-extrabold tracking-tight text-white">{{ setting('footer_logo_text', 'BusPariwisata') }}</h2>
                    @endif
                </div>
                <p class="text-sm leading-6 text-slate-400 mb-6 max-w-xs">{{ setting('footer_description', 'Penyedia jasa transportasi bus pariwisata dengan pengalaman lebih dari 70 tahun. Mengutamakan keselamatan dan kenyamanan.') }}</p>
                <div class="flex items-center gap-3">
                    @foreach($socialLinks as $social)
                        <a class="size-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors" href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" {{ $social['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                            <x-fa-icon :name="$social['icon']" :style="$social['style']" class="text-sm" />
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white mb-4">Tautan Cepat</h4>
                <ul class="space-y-3 text-sm text-slate-400 leading-6">
                    <li><a class="hover:text-primary transition-colors" href="{{ route('home') }}">Beranda</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index') }}">Armada</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ route('packages.index') }}">Paket Sewa</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ route('contact.index') }}">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white mb-4">Kontak Kami</h4>
                <ul class="space-y-3 text-sm text-slate-400 leading-6">
                    <li class="flex gap-3">
                        <x-fa-icon name="location-dot" class="fa-fw text-primary text-sm" />
                        {{ setting('contact_address', 'Sulawesi Selatan, Indonesia') }}
                    </li>
                    <li class="flex gap-3">
                        <x-fa-icon name="phone" class="fa-fw text-primary text-sm" />
                        {{ setting('contact_phone', '(021) 1234 5678') }}
                    </li>
                    <li class="flex gap-3">
                        <x-fa-icon name="envelope" class="fa-fw text-primary text-sm" />
                        {{ setting('contact_email', 'info@buspariwisata.co.id') }}
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide text-white mb-4">Lokasi Kami</h4>
                @php
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
                <div class="h-40 rounded-lg bg-slate-800 overflow-hidden">
                    @if($footerMapSrc)
                        <iframe class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $footerMapSrc }}" allowfullscreen></iframe>
                    @elseif(setting('footer_map_image'))
                        <img class="w-full h-full object-cover grayscale opacity-75 hover:grayscale-0 transition-all duration-300" src="{{ setting('footer_map_image') }}" alt="Peta lokasi kantor" width="640" height="320" loading="lazy" decoding="async"/>
                    @else
                        <img class="w-full h-full object-cover grayscale opacity-50" data-alt="Peta lokasi kantor pusat" data-location="Sulawesi Selatan" src="/stitch_img_map.jpg" width="640" height="320" loading="lazy" decoding="async"/>
                    @endif
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 mt-10 border-t border-slate-800 text-center text-xs text-slate-500">
            &copy; 2026 Ragambus - Sewa Bus Pariwisata. All Rights Reserved.
        </div>
    </footer>
@endif

@if($whatsappNumber)
    <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[60] inline-flex items-center gap-2 rounded-full bg-emerald-600 px-3.5 sm:px-5 py-3 text-sm font-bold text-white shadow-xl shadow-emerald-900/20 hover:bg-emerald-700 transition-colors">
        <x-fa-icon name="whatsapp" style="brands" class="text-lg" />
        <span class="hidden sm:inline">Hubungi Kami</span>
    </a>
@endif
