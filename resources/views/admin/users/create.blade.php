@extends('layouts.admin')

@section('title', 'Tambah Akun Pengguna - Panel Admin')
@section('header_title', 'Tambah Akun Pengguna')

@section('content')
<div class="max-w-3xl">
    <form action="{{ route('admin.settings.users.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-6">
        @csrf

        @include('admin.users._form')

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.settings.users.index') }}" class="px-4 py-2 text-sm font-semibold border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Batal
            </a>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg text-sm font-bold transition-colors shadow-lg shadow-primary/20">
                Simpan Akun
            </button>
        </div>
    </form>
</div>
@endsection
