@extends('layouts.admin')

@section('title', 'Kalender Booking - Panel Admin')
@section('header_title', 'Kalender Booking')

@php
    $monthLabel = $month->translatedFormat('F Y');
    $previousMonth = $month->copy()->subMonth()->format('Y-m');
    $nextMonth = $month->copy()->addMonth()->format('Y-m');
    $weekDays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $totalBookings = $bookings->count();
    $paidBookings = $bookings->where('is_paid', true)->where('is_cancelled', false)->count();
    $cancelledBookings = $bookings->where('is_cancelled', true)->count();
    $pendingBookings = $bookings->where('is_paid', false)->where('is_cancelled', false)->count();
@endphp

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold">Data Booking Terbaru</h3>
                <p class="text-xs text-slate-500 mt-1">Pantau jadwal, status pembayaran, dan aksi booking dalam satu halaman.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.bookings.export-csv') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-sm font-bold hover:border-primary hover:text-primary transition-colors">
                    <x-fa-icon name="file-csv" class="fa-fw text-sm" />
                    Export CSV
                </a>
                <a href="{{ route('admin.bookings.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-white text-sm font-bold hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                    <x-fa-icon name="plus" class="fa-fw text-sm" />
                    Tambah Booking
                </a>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 px-3 py-2.5">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Total</p>
                <p class="text-lg font-bold mt-0.5">{{ $totalBookings }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50/80 px-3 py-2.5">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Lunas</p>
                <p class="text-lg font-bold mt-0.5 text-emerald-700">{{ $paidBookings }}</p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50/80 px-3 py-2.5">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700">Pending</p>
                <p class="text-lg font-bold mt-0.5 text-amber-700">{{ $pendingBookings }}</p>
            </div>
            <div class="rounded-lg border border-rose-200 bg-rose-50/80 px-3 py-2.5">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-rose-700">Dibatalkan</p>
                <p class="text-lg font-bold mt-0.5 text-rose-700">{{ $cancelledBookings }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-4">
            @if($month->greaterThan($currentMonth))
                <a href="{{ route('admin.bookings.index', ['month' => $previousMonth]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                    <x-fa-icon name="chevron-left" class="text-xs" />
                    Bulan Sebelumnya
                </a>
            @else
                <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed">
                    <x-fa-icon name="chevron-left" class="text-xs" />
                    Bulan Sebelumnya
                </span>
            @endif
            <h3 class="text-sm sm:text-base font-bold">{{ $monthLabel }}</h3>
            <a href="{{ route('admin.bookings.index', ['month' => $nextMonth]) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-semibold hover:border-primary hover:text-primary transition-colors">
                Bulan Berikutnya
                <x-fa-icon name="chevron-right" class="text-xs" />
            </a>
        </div>

        <div class="grid grid-cols-7 gap-2 mb-2">
            @foreach($weekDays as $weekDay)
                <div class="text-[11px] uppercase tracking-wide font-bold text-slate-500 text-center py-1">{{ $weekDay }}</div>
            @endforeach
        </div>

        <div class="space-y-2">
            @foreach($calendarWeeks as $week)
                <div class="grid grid-cols-7 gap-2">
                    @foreach($week as $day)
                        <div class="min-h-[120px] rounded-lg border p-2 {{ $day['in_month'] ? 'border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60' : 'border-slate-100 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-900/50 opacity-60' }}">
                            <p class="text-[11px] font-bold {{ $day['in_month'] ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400 dark:text-slate-500' }}">
                                {{ $day['date']->format('d') }}
                            </p>
                            <div class="mt-2 space-y-1">
                                @foreach(collect($day['bookings'])->take(3) as $dayBooking)
                                    <a href="{{ route('admin.bookings.invoice', $dayBooking) }}" class="block rounded-md px-1.5 py-1 text-[10px] leading-tight font-semibold {{ $dayBooking->is_cancelled ? 'bg-rose-100 text-rose-700' : ($dayBooking->is_paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $dayBooking->booking_code }}
                                    </a>
                                @endforeach

                                @if(count($day['bookings']) > 3)
                                    <p class="text-[10px] text-slate-500">+{{ count($day['bookings']) - 3 }} booking</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h4 class="text-sm font-bold">Daftar Booking</h4>
            <p class="text-[11px] text-slate-500">Update terakhir: {{ now()->format('d M Y H:i') }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Kode</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Customer</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Tanggal</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Layanan</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">PO / Unit</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Nominal</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500">Status</th>
                        <th class="py-3 px-3 text-xs font-bold text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr class="border-b border-slate-100 dark:border-slate-800 last:border-0 hover:bg-slate-50/70 dark:hover:bg-slate-800/60 transition-colors">
                            <td class="py-3 px-3 text-xs font-bold">{{ $booking->booking_code }}</td>
                            <td class="py-3 px-3 text-xs align-top">
                                <p class="font-semibold">{{ $booking->customer_name }}</p>
                                <p>{{ $booking->customer_phone }}</p>
                                <p class="text-slate-500">{{ $booking->departure_from }} -> {{ $booking->destination }}</p>
                                <p class="text-slate-500">{{ $booking->pickup_time }} | {{ $booking->pickup_location }}</p>
                            </td>
                            <td class="py-3 px-3 text-xs align-top">
                                {{ optional($booking->departure_date)->format('d M Y') }} - {{ optional($booking->return_date)->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 text-xs align-top">
                                {{ $booking->service_type }}
                            </td>
                            <td class="py-3 px-3 text-xs align-top">
                                <p>{{ filled($booking->po_key) ? gallery_po_label($booking->po_key, $booking->po_key) : '-' }}</p>
                                <p class="text-slate-500">{{ $booking->gallery?->title ?? '-' }}</p>
                                <p class="text-slate-500">Unit Dibooking: {{ number_format((int) $booking->booked_unit_count) }}</p>
                            </td>
                            <td class="py-3 px-3 text-xs align-top">
                                <p>Harga PO: Rp {{ number_format((float) $booking->deal_price, 0, ',', '.') }}</p>
                                <p>Harga Customer: Rp {{ number_format((float) $booking->markup_price, 0, ',', '.') }}</p>
                                <p class="font-semibold {{ $booking->profit_amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Profit: Rp {{ number_format((float) $booking->profit_amount, 0, ',', '.') }}
                                </p>
                                <p>DP Customer: Rp {{ number_format((float) $booking->dp_amount, 0, ',', '.') }}</p>
                                <p>DP Pemilik PO: Rp {{ number_format((float) $booking->owner_dp_amount, 0, ',', '.') }}</p>
                                @if(!$booking->is_paid && !$booking->is_cancelled)
                                    <p>Sisa: Rp {{ number_format((float) $booking->remaining_amount, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-3 align-top">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $booking->is_cancelled ? 'bg-rose-100 text-rose-700' : ($booking->is_paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $booking->is_cancelled ? 'Dibatalkan' : ($booking->is_paid ? 'Lunas' : 'Belum Lunas') }}
                                </span>
                            </td>
                            <td class="py-3 px-3 align-top">
                                <div class="flex justify-end items-center gap-2 flex-wrap">
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-bold hover:border-primary hover:text-primary transition-colors">
                                        <x-fa-icon name="pen-to-square" class="text-xs" />
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.bookings.toggle-paid', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold {{ $booking->is_paid ? 'border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition-colors {{ $booking->is_cancelled ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $booking->is_cancelled ? 'disabled' : '' }}>
                                            <x-fa-icon name="{{ $booking->is_paid ? 'rotate-left' : 'circle-check' }}" class="text-xs" />
                                            {{ $booking->is_paid ? 'Batal Lunas' : 'Lunas' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.toggle-cancel', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold {{ $booking->is_cancelled ? 'border border-sky-300 bg-sky-50 text-sky-700 hover:bg-sky-100' : 'border border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100' }} transition-colors">
                                            <x-fa-icon name="{{ $booking->is_cancelled ? 'rotate-left' : 'ban' }}" class="text-xs" />
                                            {{ $booking->is_cancelled ? 'Aktifkan' : 'Cancel' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.bookings.invoice', $booking) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-bold hover:border-primary hover:text-primary transition-colors">
                                        <x-fa-icon name="print" class="text-xs" />
                                        Cetak Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500 text-sm">Belum ada data booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
