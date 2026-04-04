@extends('layouts.admin')

@section('title', 'Kategori Armada - Panel Admin')
@section('header_title', 'Kategori Armada')

@section('content')
@php
    $oldCategoryKeys = old('category_keys');

    if (is_array($oldCategoryKeys)) {
        $categoryRows = collect($oldCategoryKeys)
            ->map(function ($key, $index) {
                return [
                    'original_key' => old("category_original_keys.$index"),
                    'key' => $key,
                    'label' => old("category_labels.$index"),
                    'description' => old("category_descriptions.$index"),
                ];
            })
            ->filter(fn (array $category) => filled($category['original_key']) || filled($category['key']) || filled($category['label']) || filled($category['description']))
            ->values()
            ->all();
    }
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

<form action="{{ route('admin.settings.categories.update') }}" method="POST" class="space-y-8" data-category-form>
    @csrf

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <x-fa-icon name="bus" class="fa-fw text-primary" /> Daftar Kategori Armada
                </h3>
                <p class="text-xs text-slate-500 mt-2 max-w-2xl">
                    Atur kategori armada dari sini. Perubahan kategori akan langsung dipakai di form tambah/edit armada, filter katalog, badge armada, dan link kategori di footer.
                </p>
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold hover:border-primary hover:text-primary transition-colors" data-add-category-row>
                <x-fa-icon name="plus" class="fa-fw text-sm" />
                Tambah Kategori
            </button>
        </div>

        @error('category_keys')
            <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        <div class="space-y-4" data-category-list>
            @foreach($categoryRows as $index => $category)
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-category-row>
                    <input type="hidden" name="category_original_keys[]" value="{{ $category['original_key'] ?? '' }}">
                    <div class="grid grid-cols-1 xl:grid-cols-[1.1fr_1.4fr_1.4fr_auto] gap-4 items-start">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode Kategori</label>
                            <input type="text" name="category_keys[]" value="{{ $category['key'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: minibus">
                            <p class="text-[11px] text-slate-500">Gunakan huruf kecil, angka, dan tanda hubung.</p>
                            @error("category_keys.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil</label>
                            <input type="text" name="category_labels[]" value="{{ $category['label'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: Minibus">
                            @error("category_labels.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keterangan Ukuran / Filter</label>
                            <input type="text" name="category_descriptions[]" value="{{ $category['description'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: 20 - 40 Kursi">
                            <p class="text-[11px] text-slate-500">Dipakai di pilihan kategori armada dan filter kapasitas kursi katalog.</p>
                            @error("category_descriptions.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-6 xl:pt-7">
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-category-row>
                                <x-fa-icon name="trash" class="fa-fw text-sm" />
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-category-row-template>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-category-row>
                <input type="hidden" name="category_original_keys[]" value="">
                <div class="grid grid-cols-1 xl:grid-cols-[1.1fr_1.4fr_1.4fr_auto] gap-4 items-start">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode Kategori</label>
                        <input type="text" name="category_keys[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: premium-bus">
                        <p class="text-[11px] text-slate-500">Gunakan huruf kecil, angka, dan tanda hubung.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil</label>
                        <input type="text" name="category_labels[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: Premium Bus">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keterangan Ukuran / Filter</label>
                        <input type="text" name="category_descriptions[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: 30 Kursi">
                        <p class="text-[11px] text-slate-500">Dipakai di pilihan kategori armada dan filter kapasitas kursi katalog.</p>
                    </div>
                    <div class="pt-6 xl:pt-7">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-category-row>
                            <x-fa-icon name="trash" class="fa-fw text-sm" />
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-10 py-3 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all">
            Simpan Kategori
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-category-form]');
        const categoryList = document.querySelector('[data-category-list]');
        const categoryRowTemplate = document.querySelector('[data-category-row-template]');
        const addCategoryButton = document.querySelector('[data-add-category-row]');

        if (!form || !categoryList || !categoryRowTemplate || !addCategoryButton) {
            return;
        }

        const bindRemoveButtons = () => {
            categoryList.querySelectorAll('[data-remove-category-row]').forEach((button) => {
                if (button.dataset.bound === 'true') {
                    return;
                }

                button.dataset.bound = 'true';
                button.addEventListener('click', () => {
                    const rows = categoryList.querySelectorAll('[data-category-row]');

                    if (rows.length <= 1) {
                        window.alert('Minimal harus ada 1 kategori armada.');
                        return;
                    }

                    button.closest('[data-category-row]')?.remove();
                });
            });
        };

        addCategoryButton.addEventListener('click', () => {
            const fragment = categoryRowTemplate.content.cloneNode(true);
            categoryList.appendChild(fragment);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    });
</script>
@endsection
