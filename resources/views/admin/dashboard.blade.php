@extends('layouts.admin')

@section('title', 'Dashboard - Panel Admin')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Armada</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['galleries_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">{{ collect($stats['gallery_breakdown'])->map(fn ($item) => $item['label'] . ' ' . $item['count'])->implode(' | ') }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Paket</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['packages_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Aktif {{ $stats['packages_active'] }} | Sewa {{ $stats['packages_sewa'] }} | Liburan {{ $stats['packages_liburan'] }}</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Akun Pengguna</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ $stats['users_total'] }}</p>
            <p class="text-xs text-slate-500 mt-2">Total user yang terdaftar di sistem.</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Booking Tahun Ini</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">{{ number_format($stats['bookings_year_total']) }}</p>
            <p class="text-xs text-slate-500 mt-2">Akumulasi booking aktif tahun {{ now()->format('Y') }}.</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Revenue Tahun Ini</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">Rp {{ number_format((float) $stats['bookings_year_revenue'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-2">Diambil dari total harga customer (markup).</p>
        </article>
        <article class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-2">Total Profit Tahun Ini</p>
            <p class="text-3xl font-black text-slate-900 dark:text-slate-100">Rp {{ number_format((float) $stats['bookings_year_profit'], 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500 mt-2">Selisih harga customer dan harga deal PO.</p>
        </article>
    </div>

    <section class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Reminder Booking 7 Hari Ke Depan</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-primary hover:underline">Lihat Kalender Booking</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800">
                        <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Kode</th>
                        <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Customer</th>
                        <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal</th>
                        <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Rute</th>
                        <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingBookings as $booking)
                        <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                            <td class="py-3 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $booking->booking_code }}</td>
                            <td class="py-3 text-sm text-slate-600 dark:text-slate-300">
                                <p class="font-semibold">{{ $booking->customer_name }}</p>
                                <p class="text-xs text-slate-500">{{ $booking->customer_phone }}</p>
                            </td>
                            <td class="py-3 text-sm text-slate-600 dark:text-slate-300">
                                <p>{{ optional($booking->departure_date)->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">{{ $booking->pickup_time }}</p>
                            </td>
                            <td class="py-3 text-sm text-slate-600 dark:text-slate-300">{{ $booking->departure_from }} -> {{ $booking->destination }}</td>
                            <td class="py-3 text-sm text-slate-500">{{ $booking->gallery?->title ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-sm text-slate-500 text-center">Tidak ada booking terdekat dalam 7 hari ke depan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Bookingan Terbaru</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800">
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Kode</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Customer</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Layanan</th>
                            <th class="py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wide">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0">
                                <td class="py-3 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $booking->booking_code }}</td>
                                <td class="py-3 text-sm text-slate-600 dark:text-slate-300">
                                    <p class="font-semibold">{{ $booking->customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $booking->customer_phone }}</p>
                                </td>
                                <td class="py-3 text-sm text-slate-600 dark:text-slate-300">
                                    {{ optional($booking->departure_date)->format('d M Y') }}
                                </td>
                                <td class="py-3 text-sm text-slate-600 dark:text-slate-300">{{ $booking->service_type }}</td>
                                <td class="py-3 text-sm text-slate-500">{{ $booking->gallery?->title ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-sm text-slate-500 text-center">Belum ada data booking terbaru.</td>
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
                    <x-fa-icon name="arrow-right" class="fa-fw text-base" />
                </a>
                <a href="{{ route('admin.rental-packages.create') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                    Tambah Paket
                    <x-fa-icon name="arrow-right" class="fa-fw text-base" />
                </a>
                @if(auth()->user()->canAccessSettings())
                    <a href="{{ route('admin.settings.index') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                        Lengkapi Pengaturan Homepage
                        <x-fa-icon name="arrow-right" class="fa-fw text-base" />
                    </a>
                @endif
                @if(auth()->user()->canManageUsers())
                    <a href="{{ route('admin.settings.users.index') }}" class="w-full inline-flex items-center justify-between rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-semibold hover:border-primary hover:text-primary transition-colors">
                        Manajemen Akun Pengguna
                        <x-fa-icon name="arrow-right" class="fa-fw text-base" />
                    </a>
                @endif
            </div>

            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-6 mb-3">Paket Terbaru</h4>
            <div class="space-y-2">
                @forelse($recentPackages as $package)
                    <div class="rounded-lg border border-slate-200 dark:border-slate-800 px-3 py-2">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $package->title }}</p>
                        <p class="text-xs text-slate-500">{{ $package->type === 'liburan' ? 'Liburan' : 'Sewa' }} | {{ $package->updated_at?->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada paket terbaru.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
