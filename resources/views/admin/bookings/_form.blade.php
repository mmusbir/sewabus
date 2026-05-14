@php
    $rupiahInput = static function ($value): string {
        if (!filled($value) && $value !== 0 && $value !== '0') {
            return '';
        }

        $normalized = (string) $value;
        $normalized = rtrim(rtrim($normalized, '0'), '.');

        return $normalized === '' ? '0' : $normalized;
    };

    $todayDate = now()->format('Y-m-d');
@endphp

<div class="space-y-5">
    <section class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 bg-slate-50/60 dark:bg-slate-900/40 space-y-4">
        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Informasi Customer</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Nama Customer</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: Andi Saputra" required>
                @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">No HP Customer</label>
                <input type="text" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: 08xxxxxxxxxx" required>
                @error('customer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="text-xs font-semibold text-slate-500">Alamat / Titik Penjemputan</label>
            <textarea name="pickup_location" rows="3" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: Jl. AP Pettarani No. 10, Makassar" required>{{ old('pickup_location', $booking->pickup_location ?? '') }}</textarea>
            @error('pickup_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 bg-slate-50/60 dark:bg-slate-900/40 space-y-4">
        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Jadwal & Rute</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Berangkat Dari</label>
                <input type="text" name="departure_from" value="{{ old('departure_from', $booking->departure_from ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: Makassar" required>
                @error('departure_from') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Tujuan</label>
                <input type="text" name="destination" value="{{ old('destination', $booking->destination ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: Toraja" required>
                @error('destination') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Jam Penjemputan</label>
                <input type="time" name="pickup_time" value="{{ old('pickup_time', $booking->pickup_time ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required>
                @error('pickup_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Tanggal Berangkat</label>
                <input type="date" name="departure_date" min="{{ $todayDate }}" data-departure-date value="{{ old('departure_date', isset($booking->departure_date) ? $booking->departure_date->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required>
                @error('departure_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Tanggal Pulang</label>
                <input type="date" name="return_date" min="{{ $todayDate }}" data-return-date value="{{ old('return_date', isset($booking->return_date) ? $booking->return_date->format('Y-m-d') : '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required>
                @error('return_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 bg-slate-50/60 dark:bg-slate-900/40 space-y-4">
        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Layanan & Unit</h4>

        <div>
            <label class="text-xs font-semibold text-slate-500">Jenis Layanan</label>
            <select name="service_type" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required data-service-type>
                <option value="">Pilih layanan</option>
                @foreach($serviceTypes as $serviceType)
                    <option value="{{ $serviceType }}" {{ old('service_type', $booking->service_type ?? '') === $serviceType ? 'selected' : '' }}>{{ $serviceType }}</option>
                @endforeach
            </select>
            @error('service_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div data-service-note-wrapper class="{{ old('service_type', $booking->service_type ?? '') === 'DLL' ? '' : 'hidden' }}">
            <label class="text-xs font-semibold text-slate-500">Keterangan DLL</label>
            <input type="text" name="service_type_note" value="{{ old('service_type_note', $booking->service_type_note ?? '') }}" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" placeholder="Contoh: Antar jemput event">
            @error('service_type_note') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">PO Yang Dipilih</label>
                <select name="po_key" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm">
                    <option value="">Pilih PO</option>
                    @foreach($poOptions as $poOption)
                        <option value="{{ $poOption['key'] }}" {{ old('po_key', $booking->po_key ?? '') === $poOption['key'] ? 'selected' : '' }}>
                            {{ $poOption['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('po_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Unit Kendaraan Yang Dipilih</label>
                <select name="gallery_id" class="mt-1 w-full rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm">
                    <option value="">Pilih Unit</option>
                    @foreach($galleries as $gallery)
                        <option value="{{ $gallery->id }}" {{ (string) old('gallery_id', $booking->gallery_id ?? '') === (string) $gallery->id ? 'selected' : '' }}>
                            {{ $gallery->title }}{{ filled($gallery->po_key) ? ' - '.gallery_po_label($gallery->po_key, $gallery->po_key) : '' }}
                        </option>
                    @endforeach
                </select>
                @error('gallery_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 dark:border-slate-800 p-4 sm:p-5 bg-slate-50/60 dark:bg-slate-900/40 space-y-4">
        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Finansial</h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Harga Deal</label>
                <div class="mt-1 relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-500">Rp</span>
                    <input type="number" min="0" step="1000" name="deal_price" value="{{ $rupiahInput(old('deal_price', $booking->deal_price ?? '')) }}" class="w-full rounded-lg border-slate-200 pl-10 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required>
                </div>
                <p class="mt-1 text-[11px] text-slate-500">Harga dari pemilik PO.</p>
                @error('deal_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Harga Markup</label>
                <div class="mt-1 relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-500">Rp</span>
                    <input type="number" min="0" step="1000" name="markup_price" value="{{ $rupiahInput(old('markup_price', $booking->markup_price ?? '')) }}" class="w-full rounded-lg border-slate-200 pl-10 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm" required>
                </div>
                <p class="mt-1 text-[11px] text-slate-500">Harga yang diberikan ke customer.</p>
                @error('markup_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold text-slate-500">Jumlah DP (Dari Customer)</label>
                <div class="mt-1 relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-500">Rp</span>
                    <input type="number" min="0" step="1000" name="dp_amount" value="{{ $rupiahInput(old('dp_amount', $booking->dp_amount ?? '')) }}" class="w-full rounded-lg border-slate-200 pl-10 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm">
                </div>
                @error('dp_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500">Jumlah DP ke Pemilik PO</label>
                <div class="mt-1 relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-500">Rp</span>
                    <input type="number" min="0" step="1000" name="owner_dp_amount" value="{{ $rupiahInput(old('owner_dp_amount', $booking->owner_dp_amount ?? '')) }}" class="w-full rounded-lg border-slate-200 pl-10 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm">
                </div>
                @error('owner_dp_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>
</div>
