@extends('layouts.admin')

@section('title', 'Tambah Booking - Panel Admin')
@section('header_title', 'Tambah Booking')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
        Kembali ke Data Booking
    </a>
</div>

<form method="POST" action="{{ route('admin.bookings.store') }}" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm max-w-5xl space-y-5" data-booking-form>
    @csrf

    @include('admin.bookings._form', ['booking' => null])

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2">
        <button type="submit" name="mark_as_paid" value="0" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-white text-sm font-bold hover:bg-primary/90 transition-colors">
            <x-fa-icon name="floppy-disk" class="fa-fw text-sm" />
            Simpan Booking
        </button>
        <button type="submit" name="mark_as_paid" value="1" class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-emerald-700 text-sm font-bold hover:bg-emerald-100 transition-colors">
            <x-fa-icon name="circle-check" class="fa-fw text-sm" />
            Simpan & Lunas
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('[data-booking-form]');
        if (!form) {
            return;
        }

        const serviceTypeField = form.querySelector('[data-service-type]');
        const serviceNoteWrapper = form.querySelector('[data-service-note-wrapper]');
        const departureDateField = form.querySelector('[data-departure-date]');
        const returnDateField = form.querySelector('[data-return-date]');
        if (!serviceTypeField || !serviceNoteWrapper) {
            return;
        }

        const toggleServiceNote = () => {
            const show = serviceTypeField.value === 'DLL';
            serviceNoteWrapper.classList.toggle('hidden', !show);
        };

        serviceTypeField.addEventListener('change', toggleServiceNote);
        toggleServiceNote();

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
    });
</script>
@endsection
