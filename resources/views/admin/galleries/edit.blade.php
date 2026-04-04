@extends('layouts.admin')

@section('title', 'Edit Armada - Panel Admin')
@section('header_title', 'Edit Galeri Armada')

@section('content')
@php
    $currentCoverId = old('cover_image_id');

    if ($currentCoverId === null) {
        $currentCover = $gallery->images->first(function ($image) use ($gallery) {
            return $image->getRawOriginal('media_path') === $gallery->getRawOriginal('image_path');
        });

        $currentCoverId = $currentCover?->id ?? $gallery->images->first()?->id;
    }

    $removeImageIds = collect(old('remove_image_ids', []))
        ->map(fn ($id) => (string) $id)
        ->all();
    $selectedFacilityKeys = collect(old('facility_keys', gallery_selected_catalog_facility_keys($gallery->facilities)))
        ->map(fn ($key) => (string) $key)
        ->all();
    $facilityCustom = old('facility_custom', gallery_custom_facility_text($gallery->facilities));
@endphp

<div class="mb-6">
    <a href="{{ route('admin.galleries.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
        Kembali
    </a>
</div>

<form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm max-w-2xl">
    @csrf
    @method('PUT')
    
    <div class="space-y-6">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama Armada</label>
            <input type="text" name="title" value="{{ old('title', $gallery->title) }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Nama PO</label>
            <select name="po_key" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="">-- Pilih Nama PO --</option>
                @foreach($poOptions as $poOption)
                    <option value="{{ $poOption['key'] }}" {{ old('po_key', $gallery->po_key ?? ($poOptions[0]['key'] ?? '')) === $poOption['key'] ? 'selected' : '' }}>
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
                    <option value="{{ $category['key'] }}" {{ old('category', $gallery->category) === $category['key'] ? 'selected' : '' }}>
                        {{ gallery_category_full_label($category['key']) }}
                    </option>
                @endforeach
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Jumlah Unit</label>
            <input type="number" name="unit_count" min="1" max="999" value="{{ old('unit_count', $gallery->unit_count ?? 1) }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('unit_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Status Armada</label>
            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
                Aktif (tampil di katalog)
            </label>
            @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Foto Armada (Maks 6)</label>
            @if($gallery->images->count())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    @foreach($gallery->images as $image)
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-800/60">
                            <img src="{{ $image->media_path }}" alt="{{ $gallery->title }}" class="h-32 w-full object-cover">
                            <div class="p-3 space-y-3">
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    <input type="radio" name="cover_image_id" value="{{ $image->id }}" {{ (string) $currentCoverId === (string) $image->id ? 'checked' : '' }} class="border-slate-300 text-primary focus:ring-primary">
                                    Jadikan sampul galeri
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-rose-600 dark:text-rose-400">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" {{ in_array((string) $image->id, $removeImageIds, true) ? 'checked' : '' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                    Hapus gambar ini
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500">Pilih satu foto sebagai sampul. Centang foto yang ingin dihapus saat menyimpan perubahan.</p>
            @endif
            <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                <input type="hidden" name="replace_images" value="0">
                <input type="checkbox" name="replace_images" value="1" class="rounded border-slate-300 text-primary focus:ring-primary">
                Ganti semua foto lama dengan upload baru
            </label>
            <input type="file" name="images[]" multiple accept="image/png,image/jpeg,image/jpg,image/webp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <p class="text-xs text-slate-500 mt-1">Upload tambahan foto atau centang opsi ganti semua foto. Maksimal total 6 foto.</p>
            @error('images') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('images.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('remove_image_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @error('cover_image_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Video Armada (Opsional, 1 File)</label>
            @if($gallery->video)
                <video controls class="w-full max-w-md rounded-lg border border-slate-200 bg-black">
                    <source src="{{ $gallery->video->media_path }}">
                </video>
                <label class="flex items-center gap-2 text-xs text-rose-600 mt-2">
                    <input type="hidden" name="remove_video" value="0">
                    <input type="checkbox" name="remove_video" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                    Hapus video saat ini
                </label>
            @endif
            <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <p class="text-xs text-slate-500 mt-1">Upload video baru untuk mengganti video lama. Maks 20 MB.</p>
            @error('video') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi Lengkap</label>
            <textarea name="description" rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('description', $gallery->description) }}</textarea>
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
            <x-fa-icon name="floppy-disk" class="fa-fw text-sm" /> Perbarui
        </button>
    </div>
</form>
@endsection
