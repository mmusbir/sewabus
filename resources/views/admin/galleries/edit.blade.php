@extends('layouts.admin')

@section('title', 'Edit Armada - Panel Admin')
@section('header_title', 'Edit Galeri Armada')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.galleries.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
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
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Kategori / Ukuran</label>
            <select name="category" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="">-- Pilih Kategori --</option>
                <option value="minibus" {{ old('category', $gallery->category) == 'minibus' ? 'selected' : '' }}>Minibus (&lt; 20 Kursi)</option>
                <option value="mediumbus" {{ old('category', $gallery->category) == 'mediumbus' ? 'selected' : '' }}>Mediumbus (20-40 Kursi)</option>
                <option value="bigbus" {{ old('category', $gallery->category) == 'bigbus' ? 'selected' : '' }}>Bigbus (&gt; 40 Kursi)</option>
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
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
                    @foreach($gallery->images as $image)
                        <img src="{{ $image->media_path }}" alt="{{ $gallery->title }}" class="h-24 w-full object-cover rounded-lg border border-slate-200">
                    @endforeach
                </div>
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

        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Daftar Fasilitas</label>
            <textarea name="facilities" rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm" placeholder="Tulis satu fasilitas per baris&#10;Contoh:&#10;AC Full&#10;Toilet&#10;USB Charger">{{ old('facilities', $gallery->facilities) }}</textarea>
            <p class="text-xs text-slate-500">Pisahkan fasilitas dengan baris baru agar tampil rapi di halaman detail.</p>
            @error('facilities') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex justify-end pt-8">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">save</span> Perbarui
        </button>
    </div>
</form>
@endsection
