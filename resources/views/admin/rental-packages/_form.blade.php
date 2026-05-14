@php
    $editing = isset($rentalPackage);
    $selectedType = old('type', $editing ? $rentalPackage->type : 'sewa');
    $itineraryRows = old('itinerary');
    $liburanMediaFields = [
        [
            'upload' => 'vehicle_exterior_image',
            'column' => 'vehicle_exterior_image_path',
            'label' => 'Foto Luar Unit Kendaraan',
            'helper' => 'Ambil tampak depan/samping unit.',
        ],
        [
            'upload' => 'vehicle_interior_image',
            'column' => 'vehicle_interior_image_path',
            'label' => 'Foto Dalam Unit Kendaraan',
            'helper' => 'Tampilkan kursi dan kabin dalam.',
        ],
        [
            'upload' => 'lodging_exterior_image',
            'column' => 'lodging_exterior_image_path',
            'label' => 'Foto Luar Penginapan',
            'helper' => 'Contoh fasad atau area luar hotel/penginapan.',
        ],
        [
            'upload' => 'lodging_interior_image',
            'column' => 'lodging_interior_image_path',
            'label' => 'Foto Dalam Penginapan',
            'helper' => 'Contoh kamar atau interior penginapan.',
        ],
    ];

    if (!is_array($itineraryRows)) {
        $itineraryRows = $editing ? ($rentalPackage->itinerary ?? []) : [];
    }

    $itineraryRows = collect($itineraryRows)
        ->map(function ($item, int $index) {
            $defaultDay = 'Day ' . ($index + 1);

            return [
                'day' => trim((string) data_get($item, 'day', $defaultDay)),
                'description' => trim((string) data_get($item, 'description', '')),
            ];
        })
        ->values()
        ->all();

    if ($itineraryRows === []) {
        $itineraryRows = [['day' => 'Day 1', 'description' => '']];
    }
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
            <select name="type" data-package-type required class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
                <option value="sewa" {{ $selectedType === 'sewa' ? 'selected' : '' }}>Paket Sewa</option>
                <option value="liburan" {{ $selectedType === 'liburan' ? 'selected' : '' }}>Paket Liburan</option>
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

    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Itinerary Perjalanan</label>
            <button type="button" data-itinerary-add class="inline-flex items-center gap-1 rounded-lg border border-slate-300 dark:border-slate-600 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:border-primary hover:text-primary">
                <x-fa-icon name="plus" class="fa-fw text-xs" />
                Tambah Day
            </button>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">Contoh label: Day 1, Day 2, Day 3. Kosongkan baris jika tidak dipakai.</p>
        <div data-itinerary-list class="space-y-3">
            @foreach($itineraryRows as $index => $row)
                <div data-itinerary-item class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2">
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            name="itinerary[{{ $index }}][day]"
                            value="{{ $row['day'] }}"
                            placeholder="Day 1"
                            class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
                        >
                        <button type="button" data-itinerary-remove class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 text-slate-500 hover:text-red-500 hover:border-red-300" aria-label="Hapus day">
                            <x-fa-icon name="trash" class="fa-fw text-xs" />
                        </button>
                    </div>
                    <textarea
                        name="itinerary[{{ $index }}][description]"
                        rows="3"
                        placeholder="Tulis rencana perjalanan untuk day ini"
                        class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
                    >{{ $row['description'] }}</textarea>
                </div>
            @endforeach
        </div>
        @error('itinerary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('itinerary.*.day') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('itinerary.*.description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Yang Tidak Termasuk (Exclude)</label>
        <textarea name="excludes" rows="4" placeholder="Tulis satu item per baris" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('excludes', $editing ? $rentalPackage->excludes : '') }}</textarea>
        @error('excludes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Syarat &amp; Ketentuan</label>
        <textarea name="terms_conditions" rows="4" placeholder="Tulis syarat dan ketentuan paket" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">{{ old('terms_conditions', $editing ? $rentalPackage->terms_conditions : '') }}</textarea>
        @error('terms_conditions') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div data-liburan-media-wrapper class="space-y-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/40 p-4 {{ $selectedType === 'liburan' ? '' : 'hidden' }}">
        <div>
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Media Paket Liburan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Upload dokumentasi unit dan penginapan. Maksimal 3 MB per foto.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($liburanMediaFields as $field)
                @php
                    $currentMedia = $editing ? ($rentalPackage->{$field['column']} ?? null) : null;
                @endphp
                <div class="space-y-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/60 p-3">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $field['label'] }}</label>
                    @if($currentMedia)
                        <img src="{{ $currentMedia }}" alt="{{ $field['label'] }}" class="h-28 w-full rounded-lg border border-slate-200 object-cover">
                    @endif
                    <input type="file" name="{{ $field['upload'] }}" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $field['helper'] }}</p>
                    @error($field['upload']) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editing ? (int) $rentalPackage->is_active : 1) ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary">
        Paket aktif ditampilkan
    </label>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.querySelector('[data-package-type]');
    const liburanMediaWrapper = document.querySelector('[data-liburan-media-wrapper]');
    const itineraryList = document.querySelector('[data-itinerary-list]');
    const addButton = document.querySelector('[data-itinerary-add]');

    const syncLiburanMediaVisibility = () => {
        if (!typeSelect || !liburanMediaWrapper) {
            return;
        }

        const isLiburan = typeSelect.value === 'liburan';
        liburanMediaWrapper.classList.toggle('hidden', !isLiburan);
    };

    if (typeSelect) {
        typeSelect.addEventListener('change', syncLiburanMediaVisibility);
        syncLiburanMediaVisibility();
    }

    if (!itineraryList || !addButton) {
        return;
    }

    const reindexItems = () => {
        const items = itineraryList.querySelectorAll('[data-itinerary-item]');
        items.forEach((item, index) => {
            const dayInput = item.querySelector('input[name*="[day]"]');
            const descriptionInput = item.querySelector('textarea[name*="[description]"]');
            if (dayInput) {
                dayInput.name = `itinerary[${index}][day]`;
                if (!dayInput.value.trim()) {
                    dayInput.value = `Day ${index + 1}`;
                }
            }
            if (descriptionInput) {
                descriptionInput.name = `itinerary[${index}][description]`;
            }
        });
    };

    const createItem = (index) => {
        const wrapper = document.createElement('div');
        wrapper.setAttribute('data-itinerary-item', '');
        wrapper.className = 'rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-3 space-y-2';
        wrapper.innerHTML = `
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="itinerary[${index}][day]"
                    value="Day ${index + 1}"
                    placeholder="Day 1"
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
                >
                <button type="button" data-itinerary-remove class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 text-slate-500 hover:text-red-500 hover:border-red-300" aria-label="Hapus day">
                    <i class="fa-solid fa-trash fa-fw text-xs"></i>
                </button>
            </div>
            <textarea
                name="itinerary[${index}][description]"
                rows="3"
                placeholder="Tulis rencana perjalanan untuk day ini"
                class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm"
            ></textarea>
        `;
        return wrapper;
    };

    addButton.addEventListener('click', function () {
        const newIndex = itineraryList.querySelectorAll('[data-itinerary-item]').length;
        itineraryList.appendChild(createItem(newIndex));
        reindexItems();
    });

    itineraryList.addEventListener('click', function (event) {
        const removeButton = event.target.closest('[data-itinerary-remove]');
        if (!removeButton) {
            return;
        }

        const item = removeButton.closest('[data-itinerary-item]');
        if (!item) {
            return;
        }

        item.remove();
        if (itineraryList.querySelectorAll('[data-itinerary-item]').length === 0) {
            itineraryList.appendChild(createItem(0));
        }
        reindexItems();
    });

    reindexItems();
});
</script>
