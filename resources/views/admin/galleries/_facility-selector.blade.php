@props([
    'facilityOptions' => [],
    'selectedFacilityKeys' => [],
    'facilityCustom' => '',
])

<div class="space-y-2" data-facility-selector>
    <div class="flex items-center justify-between gap-3">
        <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Fasilitas Armada</label>
        <span class="text-xs font-semibold text-slate-500" data-facility-selected-count>0 dipilih</span>
    </div>

    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_auto] gap-3 items-center">
            <div class="relative">
                <x-fa-icon name="magnifying-glass" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm" />
                <input
                    type="text"
                    value=""
                    placeholder="Cari fasilitas..."
                    class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 pl-10 text-sm"
                    data-facility-search
                >
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-primary hover:text-primary transition-colors" data-facility-select-visible>
                    Pilih Terlihat
                </button>
                <button type="button" class="rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-primary hover:text-primary transition-colors" data-facility-clear>
                    Reset
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" data-facility-option-list>
            @foreach($facilityOptions as $facility)
                <label
                    class="flex items-center gap-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-700 dark:text-slate-200"
                    data-facility-option
                    data-facility-text="{{ strtolower($facility['label'].' '.implode(' ', $facility['keywords'] ?? [])) }}"
                >
                    <input
                        type="checkbox"
                        name="facility_keys[]"
                        value="{{ $facility['key'] }}"
                        {{ in_array($facility['key'], $selectedFacilityKeys, true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-primary focus:ring-primary"
                        data-facility-checkbox
                    >
                    <span>{{ $facility['label'] }}</span>
                </label>
            @endforeach
        </div>

        <p class="hidden rounded-lg border border-dashed border-slate-200 dark:border-slate-700 px-3 py-4 text-center text-sm text-slate-500" data-facility-empty-state>
            Tidak ada fasilitas yang cocok dengan pencarian.
        </p>

        <div class="space-y-2">
            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Fasilitas Tambahan</label>
            <textarea name="facility_custom" rows="4" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Tulis fasilitas tambahan satu per baris&#10;Contoh:&#10;USB Charger&#10;Reclining Seat">{{ $facilityCustom }}</textarea>
            <p class="text-xs text-slate-500">Checklist dipakai untuk filter katalog. Kolom ini untuk fasilitas tambahan di luar daftar.</p>
        </div>
    </div>

    @error('facility_keys') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('facility_keys.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    @error('facility_custom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-facility-selector]').forEach((selector) => {
            if (selector.dataset.bound === 'true') {
                return;
            }

            selector.dataset.bound = 'true';

            const searchInput = selector.querySelector('[data-facility-search]');
            const selectedCount = selector.querySelector('[data-facility-selected-count]');
            const selectVisibleButton = selector.querySelector('[data-facility-select-visible]');
            const clearButton = selector.querySelector('[data-facility-clear]');
            const emptyState = selector.querySelector('[data-facility-empty-state]');
            const optionRows = Array.from(selector.querySelectorAll('[data-facility-option]'));
            const checkboxes = Array.from(selector.querySelectorAll('[data-facility-checkbox]'));

            const updateSelectedCount = () => {
                const total = checkboxes.filter((checkbox) => checkbox.checked).length;
                selectedCount.textContent = `${total} dipilih`;
            };

            const applySearch = () => {
                const keyword = (searchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                optionRows.forEach((row) => {
                    const haystack = row.dataset.facilityText || '';
                    const isVisible = keyword === '' || haystack.includes(keyword);

                    row.classList.toggle('hidden', !isVisible);
                    if (isVisible) {
                        visibleCount++;
                    }
                });

                emptyState?.classList.toggle('hidden', visibleCount !== 0);
            };

            searchInput?.addEventListener('input', applySearch);

            selectVisibleButton?.addEventListener('click', () => {
                optionRows.forEach((row) => {
                    if (row.classList.contains('hidden')) {
                        return;
                    }

                    const checkbox = row.querySelector('[data-facility-checkbox]');

                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                updateSelectedCount();
            });

            clearButton?.addEventListener('click', () => {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });

                updateSelectedCount();
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            updateSelectedCount();
            applySearch();
        });
    });
</script>
