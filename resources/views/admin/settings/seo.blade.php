@extends('layouts.admin')

@section('title', 'SEO - Panel Admin')
@section('header_title', 'Pengaturan SEO')

@section('content')
@php
    $seoOgImageUrl = media_url($settings['seo_og_image'] ?? null);
@endphp
<form action="{{ route('admin.settings.seo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <x-fa-icon name="route" class="fa-fw text-primary" />
            SEO Global
        </h3>

        <div class="grid grid-cols-1 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Default Meta Title</label>
                <input type="text" name="seo_meta_title_default" value="{{ old('seo_meta_title_default', $settings['seo_meta_title_default'] ?? setting('site_name', 'Sewa Bus Sulawesi Selatan')) }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <p class="text-xs text-slate-500">Disarankan 50-60 karakter.</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Default Meta Description</label>
                <textarea name="seo_meta_description_default" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('seo_meta_description_default', $settings['seo_meta_description_default'] ?? 'Sewa bus pariwisata untuk semua kabupaten/kota Sulawesi Selatan dengan armada lengkap dan harga transparan.') }}</textarea>
                <p class="text-xs text-slate-500">Disarankan 140-160 karakter.</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Default Keywords</label>
                <input type="text" name="seo_meta_keywords_default" value="{{ old('seo_meta_keywords_default', $settings['seo_meta_keywords_default'] ?? 'sewa bus sulawesi selatan, sewa bus makassar, rental bus bone, bus wisata sulsel') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <p class="text-xs text-slate-500">Pisahkan dengan koma.</p>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">OG Image (Share Preview)</label>
                @if($seoOgImageUrl)
                    <img src="{{ $seoOgImageUrl }}" class="h-24 rounded-lg border border-slate-200 dark:border-slate-700 object-cover">
                @endif
                <input type="file" name="seo_og_image" accept="image/png,image/jpeg,image/jpg,image/webp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                <p class="text-xs text-slate-500">Format PNG/JPG/WEBP. Maks 4 MB.</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <x-fa-icon name="file-lines" class="fa-fw text-primary" />
            SEO Per Halaman
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Title Homepage</label>
                <input type="text" name="seo_home_title" value="{{ old('seo_home_title', $settings['seo_home_title'] ?? '') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Description Homepage</label>
                <textarea name="seo_home_description" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('seo_home_description', $settings['seo_home_description'] ?? '') }}</textarea>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Title Katalog</label>
                <input type="text" name="seo_katalog_title" value="{{ old('seo_katalog_title', $settings['seo_katalog_title'] ?? '') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Description Katalog</label>
                <textarea name="seo_katalog_description" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('seo_katalog_description', $settings['seo_katalog_description'] ?? '') }}</textarea>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Title Paket</label>
                <input type="text" name="seo_packages_title" value="{{ old('seo_packages_title', $settings['seo_packages_title'] ?? '') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Description Paket</label>
                <textarea name="seo_packages_description" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('seo_packages_description', $settings['seo_packages_description'] ?? '') }}</textarea>
            </div>
            <div class="space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Title Kontak</label>
                <input type="text" name="seo_contact_title" value="{{ old('seo_contact_title', $settings['seo_contact_title'] ?? '') }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Description Kontak</label>
                <textarea name="seo_contact_description" rows="3" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('seo_contact_description', $settings['seo_contact_description'] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-10 py-3 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all">
            Simpan SEO
        </button>
    </div>
</form>
@endsection
