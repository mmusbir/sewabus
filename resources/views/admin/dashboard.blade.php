@extends('layouts.admin')

@section('title', 'Dashboard - Panel Admin')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Armada</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['galleries_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Minibus {{ $stats['galleries_minibus'] }} • Medium {{ $stats['galleries_mediumbus'] }} • Bigbus {{ $stats['galleries_bigbus'] }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Paket</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['packages_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Aktif {{ $stats['packages_active'] }} • Sewa {{ $stats['packages_sewa'] }} • Liburan {{ $stats['packages_liburan'] }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Akun Pengguna</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['users_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Total user yang terdaftar di sistem.</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Kelengkapan Homepage</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $settingsCompletion }}%</p>
            <div class="mt-3 h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width: {{ $settingsCompletion }}%"></div>
            </div>
        </article>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Armada Terbaru</h3>
                <a href="{{ route('admin.galleries.index') }}" class="text-sm font-semibold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Armada</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Kategori</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentGalleries as $gallery)
                            <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                                <td class="py-3 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $gallery->title }}</td>
                                <td class="py-3 text-sm text-slate-600 dark:text-slate-300">{{ ucfirst($gallery->category) }}</td>
                                <td class="py-3 text-sm text-slate-500">{{ $gallery->updated_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-sm text-slate-500 text-center">Belum ada data armada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.galleries.create') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    Tambah Armada
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
                <a href="{{ route('admin.rental-packages.create') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    Tambah Paket
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
                @if(auth()->user()->canAccessSettings())
                    <a href="{{ route('admin.settings.index') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                        Lengkapi Pengaturan Homepage
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                @endif
                @if(auth()->user()->canManageUsers())
                    <a href="{{ route('admin.settings.users.index') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                        Manajemen Akun Pengguna
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                @endif
            </div>

            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-6 mb-3">Paket Terbaru</h4>
            <div class="space-y-2">
                @forelse($recentPackages as $package)
                    <div class="rounded-lg border border-slate-200 dark:border-slate-800 px-3 py-2">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $package->title }}</p>
                        <p class="text-xs text-slate-500">{{ $package->type === 'liburan' ? 'Liburan' : 'Sewa' }} • {{ $package->updated_at?->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada paket terbaru.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
