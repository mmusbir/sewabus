@extends('layouts.admin')

@section('title', 'Fasilitas Katalog - Panel Admin')
@section('header_title', 'Fasilitas Katalog')

@section('content')
@php
    $oldFacilityKeys = old('facility_keys');

    if (is_array($oldFacilityKeys)) {
        $facilityRows = collect($oldFacilityKeys)
            ->map(function ($key, $index) {
                return [
                    'original_key' => old("facility_original_keys.$index"),
                    'key' => $key,
                    'label' => old("facility_labels.$index"),
                    'keywords' => old("facility_keywords.$index"),
                ];
            })
            ->filter(fn (array $facility) => filled($facility['original_key']) || filled($facility['key']) || filled($facility['label']) || filled($facility['keywords']))
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

<form action="{{ route('admin.settings.facilities.update') }}" method="POST" class="space-y-8" data-facility-form>
    @csrf

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <x-fa-icon name="list-check" class="fa-fw text-primary" /> Daftar Fasilitas Filter
                </h3>
                <p class="text-xs text-slate-500 mt-2 max-w-2xl">
                    Fasilitas di sini akan dipakai sebagai checkbox filter pada halaman katalog. Keyword dipakai untuk mencocokkan isi fasilitas dan deskripsi armada.
                </p>
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold hover:border-primary hover:text-primary transition-colors" data-add-facility-row>
                <x-fa-icon name="plus" class="fa-fw text-sm" />
                Tambah Fasilitas
            </button>
        </div>

        @error('facility_keys')
            <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        <div class="space-y-4" data-facility-list>
            @foreach($facilityRows as $index => $facility)
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-facility-row>
                    <input type="hidden" name="facility_original_keys[]" value="{{ $facility['original_key'] ?? '' }}">
                    <div class="grid grid-cols-1 xl:grid-cols-[1fr_1.2fr_1.8fr_auto] gap-4 items-start">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode Fasilitas</label>
                            <input type="text" name="facility_keys[]" value="{{ $facility['key'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: wifi">
                            <p class="text-[11px] text-slate-500">Gunakan huruf kecil dan angka. Jika memakai underscore, sistem akan otomatis mengubahnya menjadi tanda hubung.</p>
                            @error("facility_keys.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil</label>
                            <input type="text" name="facility_labels[]" value="{{ $facility['label'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: WiFi">
                            @error("facility_labels.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keyword Pencocokan</label>
                            <input type="text" name="facility_keywords[]" value="{{ $facility['keywords'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: wifi, internet, hotspot">
                            <p class="text-[11px] text-slate-500">Pisahkan beberapa keyword dengan koma.</p>
                            @error("facility_keywords.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-6 xl:pt-7">
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-facility-row>
                                <x-fa-icon name="trash" class="fa-fw text-sm" />
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-facility-row-template>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-facility-row>
                <input type="hidden" name="facility_original_keys[]" value="">
                <div class="grid grid-cols-1 xl:grid-cols-[1fr_1.2fr_1.8fr_auto] gap-4 items-start">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode Fasilitas</label>
                        <input type="text" name="facility_keys[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: usb-charger">
                        <p class="text-[11px] text-slate-500">Gunakan huruf kecil dan angka. Jika memakai underscore, sistem akan otomatis mengubahnya menjadi tanda hubung.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil</label>
                        <input type="text" name="facility_labels[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: USB Charger">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Keyword Pencocokan</label>
                        <input type="text" name="facility_keywords[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: charger, usb, charging">
                        <p class="text-[11px] text-slate-500">Pisahkan beberapa keyword dengan koma.</p>
                    </div>
                    <div class="pt-6 xl:pt-7">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-facility-row>
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
            Simpan Fasilitas
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-facility-form]');
        const facilityList = document.querySelector('[data-facility-list]');
        const facilityRowTemplate = document.querySelector('[data-facility-row-template]');
        const addFacilityButton = document.querySelector('[data-add-facility-row]');

        if (!form || !facilityList || !facilityRowTemplate || !addFacilityButton) {
            return;
        }

        const bindRemoveButtons = () => {
            facilityList.querySelectorAll('[data-remove-facility-row]').forEach((button) => {
                if (button.dataset.bound === 'true') {
                    return;
                }

                button.dataset.bound = 'true';
                button.addEventListener('click', () => {
                    const rows = facilityList.querySelectorAll('[data-facility-row]');

                    if (rows.length <= 1) {
                        window.alert('Minimal harus ada 1 fasilitas katalog.');
                        return;
                    }

                    button.closest('[data-facility-row]')?.remove();
                });
            });
        };

        addFacilityButton.addEventListener('click', () => {
            const fragment = facilityRowTemplate.content.cloneNode(true);
            facilityList.appendChild(fragment);
            bindRemoveButtons();
        });

        bindRemoveButtons();
    });
</script>
@endsection
