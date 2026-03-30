@php
    $editing = isset($rentalPackage);
@endphp

<div class="space-y-6">
    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Judul Paket</label>
        <input type="text" name="title" value="{{ old('title', $editing ? $rentalPackage->title : '') }}" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Jenis Paket</label>
            <select name="type" required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="sewa" {{ old('type', $editing ? $rentalPackage->type : 'sewa') === 'sewa' ? 'selected' : '' }}>Paket Sewa</option>
                <option value="liburan" {{ old('type', $editing ? $rentalPackage->type : 'sewa') === 'liburan' ? 'selected' : '' }}>Paket Liburan</option>
            </select>
            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Urutan Tampil</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $editing ? $rentalPackage->sort_order : 0) }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Label Harga</label>
            <input type="text" name="price_label" value="{{ old('price_label', $editing ? $rentalPackage->price_label : '') }}" placeholder="Contoh: Mulai 2.500.000/hari" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('price_label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Durasi</label>
            <input type="text" name="duration" value="{{ old('duration', $editing ? $rentalPackage->duration : '') }}" placeholder="Contoh: 3 Hari 2 Malam" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Gambar Paket</label>
        @if($editing && $rentalPackage->image_path)
            <div class="mb-2">
                <img src="{{ $rentalPackage->image_path }}" alt="{{ $rentalPackage->title }}" class="h-32 object-cover rounded-lg border border-slate-200">
            </div>
        @endif
        <input type="file" name="image" {{ $editing ? '' : 'required' }} class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
        @if($editing)
            <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
        @endif
        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi Paket</label>
        <textarea name="description" rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('description', $editing ? $rentalPackage->description : '') }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Fasilitas / Include</label>
        <textarea name="includes" rows="4" placeholder="Tulis satu item per baris" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('includes', $editing ? $rentalPackage->includes : '') }}</textarea>
        @error('includes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editing ? (int) $rentalPackage->is_active : 1) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
        Paket aktif ditampilkan
    </label>
</div>
