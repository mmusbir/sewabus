@php
    $variant = $variant ?? 'home';
    $whatsappNumber = preg_replace('/[^0-9]/', '', setting('social_whatsapp_number', ''));
    $whatsappHref = $whatsappNumber ? "https://wa.me/{$whatsappNumber}" : '#';
    $socialLinks = [
        [
            'label' => 'Facebook',
            'href' => setting('social_facebook_url', '#'),
            'path' => 'M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.326 24h11.495v-9.294H9.691v-3.622h3.13V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.31h3.587l-.467 3.622h-3.12V24h6.116C23.403 24 24 23.403 24 22.674V1.326C24 .597 23.403 0 22.675 0z',
        ],
        [
            'label' => 'Instagram',
            'href' => setting('social_instagram_url', '#'),
            'path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
        ],
        [
            'label' => 'TikTok',
            'href' => setting('social_tiktok_url', '#'),
            'path' => 'M12.96 0c.8 0 1.6.08 2.38.24v3.24c1.24.92 2.74 1.44 4.38 1.44v3.2c-1.84 0-3.56-.54-4.98-1.46v7.02c0 3.52-2.86 6.38-6.38 6.38-3.52 0-6.38-2.86-6.38-6.38 0-3.52 2.86-6.38 6.38-6.38.54 0 1.06.06 1.56.2v3.38c-.5-.18-1.02-.28-1.56-.28-1.76 0-3.2 1.44-3.2 3.2 0 1.76 1.44 3.2 3.2 3.2 1.76 0 3.2-1.44 3.2-3.2V0h1.4z',
        ],
        [
            'label' => 'WhatsApp',
            'href' => $whatsappHref,
            'path' => 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.672.149-.198.297-.771.967-.945 1.166-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.76-1.653-2.057-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.148-.672-1.611-.92-2.206-.242-.579-.487-.5-.672-.51l-.572-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.098 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.516-5.26c.001-5.45 4.436-9.885 9.888-9.885 2.64 0 5.122 1.03 6.988 2.897 1.866 1.866 2.896 4.348 2.895 6.988-.002 5.45-4.437 9.885-9.889 9.885m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.89c0 2.096.547 4.142 1.588 5.945L0 24l6.309-1.654a11.86 11.86 0 005.737 1.46h.005c6.554 0 11.89-5.335 11.893-11.89a11.84 11.84 0 00-3.48-8.413z',
        ],
    ];
@endphp

@if($variant === 'katalog')
    <footer class="bg-white border-t border-slate-200 mt-20 pt-14 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 mb-14">
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-2 mb-5">
                        @if(setting('footer_logo_image'))
                            <img src="{{ setting('footer_logo_image') }}" alt="Logo Footer" class="h-10 object-contain">
                        @endif
                        @if(setting('footer_logo_show_text', true) && !setting('footer_logo_image'))
                            <span class="material-symbols-outlined text-primary text-3xl">directions_bus</span>
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
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index', ['category' => 'minibus']) }}">Minibus (Elf/Hiace)</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index', ['category' => 'mediumbus']) }}">Medium Bus</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index', ['category' => 'bigbus']) }}">Big Bus Executive</a></li>
                        <li><a class="hover:text-primary transition-colors" href="{{ route('katalog.index') }}">Semua Armada</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-wide text-slate-900 mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3 text-sm text-slate-600 leading-6">
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-primary text-sm mt-1">location_on</span>
                            <span>{{ setting('contact_address', 'Jl. Pariwisata No. 123, Jakarta Selatan') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-sm">phone</span>
                            <span>{{ setting('contact_phone', '(021) 1234 5678') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-sm">mail</span>
                            <span>{{ setting('contact_email', 'info@buspariwisata.co.id') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-500">&copy; 2026 Cahaya Bone - Sewa Bus Pariwisata. All Rights Reserved.</p>
                <div class="flex items-center gap-4">
                    @foreach($socialLinks as $social)
                        <a class="text-slate-400 hover:text-primary transition-colors" href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" {{ $social['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="{{ $social['path'] }}"></path>
                            </svg>
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
                    @if(setting('footer_logo_image'))
                        <img src="{{ setting('footer_logo_image') }}" alt="Logo Footer" class="h-10 object-contain filter brightness-0 invert">
                    @endif
                    @if(setting('footer_logo_show_text', true) && !setting('footer_logo_image'))
                        <span class="material-symbols-outlined text-3xl text-primary">directions_bus</span>
                        <h2 class="text-lg font-extrabold tracking-tight text-white">{{ setting('footer_logo_text', 'BusPariwisata') }}</h2>
                    @endif
                </div>
                <p class="text-sm leading-6 text-slate-400 mb-6 max-w-xs">{{ setting('footer_description', 'Penyedia jasa transportasi bus pariwisata dengan pengalaman lebih dari 70 tahun. Mengutamakan keselamatan dan kenyamanan.') }}</p>
                <div class="flex items-center gap-3">
                    @foreach($socialLinks as $social)
                        <a class="size-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors" href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" {{ $social['href'] !== '#' ? 'target="_blank" rel="noopener"' : '' }}>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="{{ $social['path'] }}"></path>
                            </svg>
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
                        <span class="material-symbols-outlined text-primary text-sm">location_on</span>
                        {{ setting('contact_address', 'Jl. Pariwisata No. 123, Jakarta Selatan') }}
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-primary text-sm">call</span>
                        {{ setting('contact_phone', '(021) 1234 5678') }}
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-primary text-sm">mail</span>
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
                        <img class="w-full h-full object-cover grayscale opacity-75 hover:grayscale-0 transition-all duration-300" src="{{ setting('footer_map_image') }}" alt="Peta lokasi kantor"/>
                    @else
                        <img class="w-full h-full object-cover grayscale opacity-50" data-alt="Peta lokasi kantor pusat" data-location="Jakarta" src="/stitch_img_map.jpg"/>
                    @endif
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 mt-10 border-t border-slate-800 text-center text-xs text-slate-500">
            &copy; 2026 Cahaya Bone - Sewa Bus Pariwisata. All Rights Reserved.
        </div>
    </footer>
@endif

@if($whatsappNumber)
    <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="fixed bottom-6 right-6 z-[60] inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-xl shadow-emerald-900/20 hover:bg-emerald-700 transition-colors">
        <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.672.149-.198.297-.771.967-.945 1.166-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.76-1.653-2.057-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.148-.672-1.611-.92-2.206-.242-.579-.487-.5-.672-.51l-.572-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.875 1.213 3.074.149.198 2.098 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.999-3.648-.235-.374a9.86 9.86 0 01-1.516-5.26c.001-5.45 4.436-9.885 9.888-9.885 2.64 0 5.122 1.03 6.988 2.897 1.866 1.866 2.896 4.348 2.895 6.988-.002 5.45-4.437 9.885-9.889 9.885m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.89c0 2.096.547 4.142 1.588 5.945L0 24l6.309-1.654a11.86 11.86 0 005.737 1.46h.005c6.554 0 11.89-5.335 11.893-11.89a11.84 11.84 0 00-3.48-8.413z"></path>
        </svg>
        Hubungi Kami
    </a>
@endif
