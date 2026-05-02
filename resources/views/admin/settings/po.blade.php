@extends('layouts.admin')

@section('title', 'Nama PO Armada - Panel Admin')
@section('header_title', 'Nama PO Armada')

@section('content')
@php
    $defaultBadgeBackground = '#334155';
    $defaultBadgeText = '#FFFFFF';
    $oldPoKeys = old('po_keys');

    if (is_array($oldPoKeys)) {
        $poRows = collect($oldPoKeys)
            ->map(function ($key, $index) {
                return [
                    'original_key' => old("po_original_keys.$index"),
                    'key' => $key,
                    'label' => old("po_labels.$index"),
                    'bg_color' => old("po_bg_colors.$index"),
                    'text_color' => old("po_text_colors.$index"),
                ];
            })
            ->filter(fn (array $poName) => filled($poName['original_key']) || filled($poName['key']) || filled($poName['label']) || filled($poName['bg_color']) || filled($poName['text_color']))
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

<form action="{{ route('admin.settings.po.update') }}" method="POST" class="space-y-8" data-po-form>
    @csrf

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <x-fa-icon name="building" class="fa-fw text-primary" /> Daftar Nama PO Armada
                </h3>
                <p class="text-xs text-slate-500 mt-2 max-w-2xl">
                    Nama PO di sini akan dipakai di dropdown form tambah/edit armada dan tampil sebagai label pada sampul kartu armada. Anda juga bisa mengatur warna background dan teks label memakai kode hex.
                </p>
            </div>
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-semibold hover:border-primary hover:text-primary transition-colors" data-add-po-row>
                <x-fa-icon name="plus" class="fa-fw text-sm" />
                Tambah Nama PO
            </button>
        </div>

        @error('po_keys')
            <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
        @enderror

        <div class="space-y-4" data-po-list>
            @foreach($poRows as $index => $poName)
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-po-row>
                    <input type="hidden" name="po_original_keys[]" value="{{ $poName['original_key'] ?? '' }}">
                    <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-[1fr_1.5fr_1fr_1fr_auto] gap-4 items-start">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode PO</label>
                            <input type="text" name="po_keys[]" value="{{ $poName['key'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: cahaya-bone">
                            <p class="text-[11px] text-slate-500">Gunakan huruf kecil, angka, dan tanda hubung.</p>
                            @error("po_keys.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil PO</label>
                            <input type="text" name="po_labels[]" value="{{ $poName['label'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: Cahaya Bone" data-po-label-input>
                            <p class="text-[11px] text-slate-500">Nama ini yang akan muncul di dropdown dan label sampul armada.</p>
                            @error("po_labels.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Warna Background</label>
                            <input type="text" name="po_bg_colors[]" value="{{ $poName['bg_color'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm uppercase" placeholder="contoh: #E16A37" data-po-bg-input>
                            <p class="text-[11px] text-slate-500">Isi kode hex 6 digit, misalnya #E16A37.</p>
                            @error("po_bg_colors.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Warna Teks</label>
                            <input type="text" name="po_text_colors[]" value="{{ $poName['text_color'] ?? '' }}" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm uppercase" placeholder="contoh: #FFFFFF" data-po-text-input>
                            <p class="text-[11px] text-slate-500">Isi kode hex 6 digit, misalnya #FFFFFF.</p>
                            @error("po_text_colors.$index") <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                        </div>
                        <div class="pt-6 2xl:pt-7">
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-po-row>
                                <x-fa-icon name="trash" class="fa-fw text-sm" />
                                Hapus
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Preview Label</span>
                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                            data-po-preview
                            style="background-color: {{ normalize_hex_color($poName['bg_color'] ?? null) ?? $defaultBadgeBackground }}; color: {{ normalize_hex_color($poName['text_color'] ?? null) ?? $defaultBadgeText }};"
                        >
                            {{ filled($poName['label'] ?? null) ? $poName['label'] : 'Preview PO' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <template data-po-row-template>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 bg-slate-50/70 dark:bg-slate-900/40" data-po-row>
                <input type="hidden" name="po_original_keys[]" value="">
                <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-[1fr_1.5fr_1fr_1fr_auto] gap-4 items-start">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Kode PO</label>
                        <input type="text" name="po_keys[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: bone-indah">
                        <p class="text-[11px] text-slate-500">Gunakan huruf kecil, angka, dan tanda hubung.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Nama Tampil PO</label>
                        <input type="text" name="po_labels[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="contoh: Bone Indah" data-po-label-input>
                        <p class="text-[11px] text-slate-500">Nama ini yang akan muncul di dropdown dan label sampul armada.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Warna Background</label>
                        <input type="text" name="po_bg_colors[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm uppercase" placeholder="contoh: #E16A37" data-po-bg-input>
                        <p class="text-[11px] text-slate-500">Isi kode hex 6 digit, misalnya #E16A37.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Warna Teks</label>
                        <input type="text" name="po_text_colors[]" value="" class="w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm uppercase" placeholder="contoh: #FFFFFF" data-po-text-input>
                        <p class="text-[11px] text-slate-500">Isi kode hex 6 digit, misalnya #FFFFFF.</p>
                    </div>
                    <div class="pt-6 2xl:pt-7">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-rose-200 px-3 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors" data-remove-po-row>
                            <x-fa-icon name="trash" class="fa-fw text-sm" />
                            Hapus
                        </button>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Preview Label</span>
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold"
                        data-po-preview
                        style="background-color: {{ $defaultBadgeBackground }}; color: {{ $defaultBadgeText }};"
                    >
                        Preview PO
                    </span>
                </div>
            </div>
        </template>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-10 py-3 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all">
            Simpan Nama PO
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-po-form]');
        const poList = document.querySelector('[data-po-list]');
        const poRowTemplate = document.querySelector('[data-po-row-template]');
        const addPoButton = document.querySelector('[data-add-po-row]');

        if (!form || !poList || !poRowTemplate || !addPoButton) {
            return;
        }

        const defaultBadgeBackground = '{{ $defaultBadgeBackground }}';
        const defaultBadgeText = '{{ $defaultBadgeText }}';

        const normalizeHexColor = (value, fallback) => {
            const normalizedValue = String(value ?? '').trim().toUpperCase();

            if (!normalizedValue) {
                return fallback;
            }

            const color = normalizedValue.startsWith('#')
                ? normalizedValue
                : `#${normalizedValue.replace(/^#+/, '')}`;

            return /^#[0-9A-F]{6}$/.test(color) ? color : fallback;
        };

        const syncPreview = (row) => {
            const labelInput = row.querySelector('[data-po-label-input]');
            const backgroundInput = row.querySelector('[data-po-bg-input]');
            const textInput = row.querySelector('[data-po-text-input]');
            const preview = row.querySelector('[data-po-preview]');

            if (!labelInput || !backgroundInput || !textInput || !preview) {
                return;
            }

            preview.textContent = labelInput.value.trim() || 'Preview PO';
            preview.style.backgroundColor = normalizeHexColor(backgroundInput.value, defaultBadgeBackground);
            preview.style.color = normalizeHexColor(textInput.value, defaultBadgeText);
        };

        const bindPreviewInputs = () => {
            poList.querySelectorAll('[data-po-row]').forEach((row) => {
                if (row.dataset.previewBound === 'true') {
                    syncPreview(row);
                    return;
                }

                row.dataset.previewBound = 'true';

                row.querySelectorAll('[data-po-label-input], [data-po-bg-input], [data-po-text-input]').forEach((input) => {
                    input.addEventListener('input', () => syncPreview(row));
                });

                syncPreview(row);
            });
        };

        const bindRemoveButtons = () => {
            poList.querySelectorAll('[data-remove-po-row]').forEach((button) => {
                if (button.dataset.bound === 'true') {
                    return;
                }

                button.dataset.bound = 'true';
                button.addEventListener('click', () => {
                    const rows = poList.querySelectorAll('[data-po-row]');

                    if (rows.length <= 1) {
                        window.alert('Minimal harus ada 1 nama PO armada.');
                        return;
                    }

                    button.closest('[data-po-row]')?.remove();
                });
            });
        };

        addPoButton.addEventListener('click', () => {
            const fragment = poRowTemplate.content.cloneNode(true);
            poList.appendChild(fragment);
            bindRemoveButtons();
            bindPreviewInputs();
        });

        bindRemoveButtons();
        bindPreviewInputs();
    });
</script>
@endsection
