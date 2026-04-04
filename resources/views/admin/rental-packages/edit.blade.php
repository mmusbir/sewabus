@extends('layouts.admin')

@section('title', 'Edit Paket - Panel Admin')
@section('header_title', 'Edit Paket Sewa / Liburan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rental-packages.index') }}" class="text-sm font-semibold text-slate-500 hover:text-primary flex items-center gap-2 w-fit">
        <x-fa-icon name="arrow-left" class="fa-fw text-sm" />
        Kembali
    </a>
</div>

<form action="{{ route('admin.rental-packages.update', $rentalPackage) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm max-w-3xl">
    @csrf
    @method('PUT')

    @include('admin.rental-packages._form')

    <div class="flex justify-end pt-8">
        <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
            <x-fa-icon name="floppy-disk" class="fa-fw text-sm" /> Perbarui
        </button>
    </div>
</form>
@endsection
