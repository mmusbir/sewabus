@extends('layouts.admin')

@section('title', 'Edit Booking - Panel Admin')
@section('header_title', 'Edit Booking')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
        Kembali ke Data Booking
    </a>
</div>

<form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm max-w-5xl space-y-5" data-booking-form>
    @csrf
    @method('PUT')

    @include('admin.bookings._form', ['booking' => $booking])

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
        <button type="submit" name="mark_as_paid" value="{{ $booking->is_paid ? 1 : 0 }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-white text-sm font-bold hover:bg-primary/90 transition-colors">
            <x-fa-icon name="floppy-disk" class="fa-fw text-sm" />
            Simpan Perubahan
        </button>
        <button type="submit" name="mark_as_paid" value="1" class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-emerald-700 text-sm font-bold hover:bg-emerald-100 transition-colors">
            <x-fa-icon name="circle-check" class="fa-fw text-sm" />
            Simpan & Tandai Lunas
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-booking-form]');
        if (!form) {
            return;
        }

        const departureDateField = form.querySelector('[data-departure-date]');
        const returnDateField = form.querySelector('[data-return-date]');
        const poSelect = form.querySelector('[data-po-select]');
        const unitSelect = form.querySelector('[data-unit-select]');

        if (departureDateField && returnDateField) {
            const syncReturnDateMin = () => {
                const departureDateValue = departureDateField.value;
                const todayMin = departureDateField.min || '';
                const minValue = departureDateValue && departureDateValue > todayMin ? departureDateValue : todayMin;

                returnDateField.min = minValue;
                if (returnDateField.value && returnDateField.value < minValue) {
                    returnDateField.value = minValue;
                }
            };

            departureDateField.addEventListener('change', syncReturnDateMin);
            syncReturnDateMin();
        }

        if (poSelect && unitSelect) {
            const allUnitOptions = Array.from(unitSelect.options).map((option) => ({
                value: option.value,
                label: option.textContent,
                poKey: option.dataset.poKey || '',
                selected: option.selected,
            }));

            const renderUnitOptions = (selectedPoKey, keepCurrentValue = true) => {
                const currentValue = keepCurrentValue ? unitSelect.value : '';
                const filtered = allUnitOptions.filter((option) => {
                    if (option.value === '') {
                        return true;
                    }

                    if (!selectedPoKey) {
                        return true;
                    }

                    return option.poKey === selectedPoKey;
                });

                unitSelect.innerHTML = '';
                filtered.forEach((optionData) => {
                    const option = document.createElement('option');
                    option.value = optionData.value;
                    option.textContent = optionData.label;
                    option.dataset.poKey = optionData.poKey;
                    unitSelect.appendChild(option);
                });

                if (keepCurrentValue && filtered.some((option) => option.value === currentValue)) {
                    unitSelect.value = currentValue;
                }
            };

            poSelect.addEventListener('change', () => {
                renderUnitOptions(poSelect.value, true);
            });

            unitSelect.addEventListener('change', () => {
                const selectedOption = unitSelect.options[unitSelect.selectedIndex];
                if (!selectedOption) {
                    return;
                }

                const poKey = selectedOption.dataset.poKey || '';
                if (!poSelect.value && poKey) {
                    poSelect.value = poKey;
                    renderUnitOptions(poSelect.value, true);
                    unitSelect.value = selectedOption.value;
                }
            });

            renderUnitOptions(poSelect.value, true);
        }
    });
</script>
@endsection
