@extends('layouts.admin')

@section('title', 'Pengaturan Homepage - Panel Admin')
@section('header_title', 'Pengaturan Homepage')

@section('content')
                @php
                    $headerLogoImageUrl = media_url($settings['header_logo_image'] ?? null);
                    $headerLogoImageDarkUrl = media_url($settings['header_logo_image_dark'] ?? null);
                    $footerLogoImageUrl = media_url($settings['footer_logo_image'] ?? null);
                    $footerMapImageUrl = media_url($settings['footer_map_image'] ?? null);
                    $heroImageUrl = media_url($settings['hero_image'] ?? null);
                    $heroImage1Url = media_url($settings['hero_image_1'] ?? null);
                    $heroImage2Url = media_url($settings['hero_image_2'] ?? null);
                    $heroImage3Url = media_url($settings['hero_image_3'] ?? null);
                @endphp
                @if($errors->any())
                    <div class="mb-6 p-4 bg-rose-100 text-rose-700 rounded-lg text-sm">
                        <p class="font-bold mb-2">Perubahan gagal disimpan:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8" data-settings-form>
                    @csrf
                    
                    <!-- Section: General -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <x-fa-icon name="globe" class="fa-fw text-primary" /> Umum
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Situs</label>
                                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Favicon</label>
                                <input type="file" name="favicon" accept="image/png,image/jpeg,image/jpg,image/x-icon" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                <p class="text-xs text-slate-500">Format: ICO/PNG/JPG. Maks 1 MB.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Branding -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <x-fa-icon name="pen-ruler" class="fa-fw text-primary" /> Branding Navbar & Footer
                        </h3>
                        <p class="text-xs text-slate-500 mb-6">Atur identitas visual untuk navbar dan footer. Jika logo gambar diisi, teks logo tetap bisa diaktifkan/nonaktifkan sesuai kebutuhan.</p>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-5 space-y-5 bg-slate-50/60 dark:bg-slate-900/40">
                                <div class="flex items-center gap-2">
                                    <x-fa-icon name="heading" class="fa-fw text-primary text-base" />
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100">Navbar Branding</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Teks Logo Navbar</label>
                                    <input type="text" name="header_logo_text" value="{{ $settings['header_logo_text'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: CahayaBone">
                                    <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                        <input type="hidden" name="header_logo_show_text" value="0">
                                        <input type="checkbox" name="header_logo_show_text" value="1" {{ old('header_logo_show_text', $settings['header_logo_show_text'] ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                                        Tampilkan teks
                                    </label>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Logo Navbar (Mode Siang)</label>
                                    <input type="file" name="header_logo_image" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Format PNG/JPG/SVG, maksimal 2 MB.</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Logo Navbar (Mode Malam)</label>
                                    <input type="file" name="header_logo_image_dark" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Opsional. Dipakai otomatis saat mode malam aktif.</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                        <p class="text-[11px] font-semibold text-slate-500 mb-2">Preview Siang</p>
                                        @if($headerLogoImageUrl)
                                            <img src="{{ $headerLogoImageUrl }}" class="h-10 object-contain">
                                        @else
                                            <p class="text-xs text-slate-400">Belum ada logo</p>
                                        @endif
                                    </div>
                                    <div class="rounded-lg border border-slate-700 bg-slate-900 p-3">
                                        <p class="text-[11px] font-semibold text-slate-400 mb-2">Preview Malam</p>
                                        @if($headerLogoImageDarkUrl)
                                            <img src="{{ $headerLogoImageDarkUrl }}" class="h-10 object-contain">
                                        @else
                                            <p class="text-xs text-slate-500">Belum ada logo</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-5 space-y-5 bg-slate-50/60 dark:bg-slate-900/40">
                                <div class="flex items-center gap-2">
                                    <x-fa-icon name="panels-stay-open" class="fa-fw text-primary text-base" />
                                    <h4 class="font-bold text-slate-800 dark:text-slate-100">Footer Branding</h4>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Teks Logo Footer</label>
                                    <input type="text" name="footer_logo_text" value="{{ $settings['footer_logo_text'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: CahayaBone">
                                    <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                        <input type="hidden" name="footer_logo_show_text" value="0">
                                        <input type="checkbox" name="footer_logo_show_text" value="1" {{ old('footer_logo_show_text', $settings['footer_logo_show_text'] ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                                        Tampilkan teks
                                    </label>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Logo Footer</label>
                                    <input type="file" name="footer_logo_image" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Format PNG/JPG/SVG, maksimal 2 MB.</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                    <p class="text-[11px] font-semibold text-slate-500 mb-2">Preview Footer</p>
                                    @if($footerLogoImageUrl)
                                        <img src="{{ $footerLogoImageUrl }}" class="h-10 object-contain">
                                    @else
                                        <p class="text-xs text-slate-400">Belum ada logo</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Hero -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <x-fa-icon name="images" class="fa-fw text-primary" /> Hero Section
                        </h3>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Judul Hero (Title)</label>
                                <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                    <input type="hidden" name="hero_show_title" value="0">
                                    <input type="checkbox" name="hero_show_title" value="1" {{ old('hero_show_title', $settings['hero_show_title'] ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                                    Tampilkan judul
                                </label>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Sub-judul Hero</label>
                                <textarea name="hero_subtitle" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                                    <input type="hidden" name="hero_show_subtitle" value="0">
                                    <input type="checkbox" name="hero_show_subtitle" value="1" {{ old('hero_show_subtitle', $settings['hero_show_subtitle'] ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                                    Tampilkan sub-judul
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Gambar Hero 1</label>
                                    @if($heroImage1Url || $heroImageUrl)
                                        <div class="mb-2">
                                            <img src="{{ $heroImage1Url ?: $heroImageUrl }}" class="h-24 w-full object-cover rounded-lg border border-slate-200">
                                        </div>
                                    @endif
                                    <input type="file" name="hero_image_1" accept="image/png,image/jpeg,image/jpg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Format: PNG/JPG. Maks 4 MB.</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Gambar Hero 2</label>
                                    @if($heroImage2Url)
                                        <div class="mb-2">
                                            <img src="{{ $heroImage2Url }}" class="h-24 w-full object-cover rounded-lg border border-slate-200">
                                        </div>
                                    @endif
                                    <input type="file" name="hero_image_2" accept="image/png,image/jpeg,image/jpg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Format: PNG/JPG. Maks 4 MB.</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Gambar Hero 3</label>
                                    @if($heroImage3Url)
                                        <div class="mb-2">
                                            <img src="{{ $heroImage3Url }}" class="h-24 w-full object-cover rounded-lg border border-slate-200">
                                        </div>
                                    @endif
                                    <input type="file" name="hero_image_3" accept="image/png,image/jpeg,image/jpg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                    <p class="text-xs text-slate-500">Format: PNG/JPG. Maks 4 MB.</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Interval Carousel (detik)</label>
                                <input type="number" min="1" max="30" name="hero_carousel_interval_seconds" value="{{ $settings['hero_carousel_interval_seconds'] ?? 5 }}" class="w-full md:w-40 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Social & Contact -->
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <x-fa-icon name="headset" class="fa-fw text-primary" /> Kontak & Sosial Media
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Link Facebook</label>
                                <input type="text" name="social_facebook_url" value="{{ $settings['social_facebook_url'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Link Instagram</label>
                                <input type="text" name="social_instagram_url" value="{{ $settings['social_instagram_url'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Link TikTok</label>
                                <input type="text" name="social_tiktok_url" value="{{ $settings['social_tiktok_url'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nomor WhatsApp (62xxx)</label>
                                <input type="text" name="social_whatsapp_number" value="{{ $settings['social_whatsapp_number'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Email</label>
                                <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Telepon</label>
                                <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Alamat</label>
                                <textarea name="contact_address" rows="2" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ $settings['contact_address'] ?? '' }}</textarea>
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi Footer</label>
                                <textarea name="footer_description" rows="2" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ $settings['footer_description'] ?? '' }}</textarea>
                            </div>
                            @php
                                $footerMapValue = $settings['footer_map_url'] ?? '';
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

                            <div class="md:col-span-2 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Link Google Maps / Koordinat</label>
                                @if(!empty($footerMapSrc))
                                    <div class="mb-2 rounded border border-slate-200 overflow-hidden">
                                        <iframe class="w-full h-48" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $footerMapSrc }}" allowfullscreen></iframe>
                                    </div>
                                @elseif($footerMapImageUrl)
                                    <div class="mb-2">
                                        <img src="{{ $footerMapImageUrl }}" class="h-32 object-cover rounded border border-slate-200">
                                    </div>
                                @endif
                                <input type="text" name="footer_map_url" value="{{ $settings['footer_map_url'] ?? '' }}" placeholder="Contoh: -6.200000,106.816666 atau https://www.google.com/maps/embed?..." class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                                <p class="text-xs text-slate-500">Masukkan koordinat (lat,lng) atau link embed Google Maps.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-10 py-3 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
@endsection
