@extends('layouts.admin')

@section('title', 'Tambah Armada - Panel Admin')
@section('header_title', 'Tambah Galeri Armada')

@section('content')
@php
    $selectedFacilityKeys = collect(old('facility_keys', []))
        ->map(fn ($key) => (string) $key)
        ->all();
    $facilityCustom = old('facility_custom', '');
@endphp

<div class="mb-6">
    <a href="{{ route('admin.galleries.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
        Kembali
    </a>
</div>

<form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm max-w-2xl">
    @csrf
    
    <div class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Armada</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama PO</label>
            <select name="po_key" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="">-- Pilih Nama PO --</option>
                @foreach($poOptions as $poOption)
                    <option value="{{ $poOption['key'] }}" {{ old('po_key') === $poOption['key'] ? 'selected' : '' }}>
                        {{ $poOption['label'] }}
                    </option>
                @endforeach
            </select>
            @error('po_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Kategori / Ukuran</label>
            <select name="category" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $category)
                    <option value="{{ $category['key'] }}" {{ old('category') === $category['key'] ? 'selected' : '' }}>
                        {{ gallery_category_full_label($category['key']) }}
                    </option>
                @endforeach
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Jumlah Unit</label>
            <input type="number" name="unit_count" min="1" max="999" value="{{ old('unit_count', 1) }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('unit_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Status Armada</label>
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                Aktif (tampil di katalog)
            </label>
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Foto Armada (Maks 6)</label>
            <input type="file" name="images[]" multiple required accept="image/png,image/jpeg,image/jpg,image/webp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <p class="text-xs text-slate-500">Upload minimal 1 foto, maksimal 6 foto. Format PNG/JPG/WEBP, masing-masing maksimal 4 MB.</p>
            @error('images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Video Armada (Opsional, 1 File)</label>
            <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <p class="text-xs text-slate-500">Format MP4/MOV/AVI/WEBM, maksimal 20 MB.</p>
            @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi Lengkap</label>
            <textarea name="description" rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        @include('admin.galleries._facility-selector', [
            'facilityOptions' => $facilityOptions,
            'selectedFacilityKeys' => $selectedFacilityKeys,
            'facilityCustom' => $facilityCustom,
        ])
    </div>

    <div class="flex justify-end pt-8">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
            <x-fa-icon name="floppy-disk" class="fa-fw text-sm" /> Simpan
        </button>
    </div>
</form>
@endsection
