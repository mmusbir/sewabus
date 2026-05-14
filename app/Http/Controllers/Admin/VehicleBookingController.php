<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\VehicleBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VehicleBookingController extends Controller
{
    private const SERVICE_TYPES = ['2D1N', 'DROP OFF', 'HALFDAY', 'FULLDAY', 'DLL'];

    public function index(Request $request)
    {
        $currentMonth = now()->startOfMonth();
        $monthInput = (string) $request->query('month', now()->format('Y-m'));
        $month = preg_match('/^\d{4}-\d{2}$/', $monthInput)
            ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
            : $currentMonth->copy();

        if ($month->lt($currentMonth)) {
            $month = $currentMonth->copy();
        }

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $bookings = VehicleBooking::with(['gallery:id,title,po_key'])
            ->whereDate('departure_date', '<=', $monthEnd)
            ->whereDate('return_date', '>=', $monthStart)
            ->orderBy('departure_date')
            ->orderBy('id')
            ->get();

        $calendarMap = $this->buildCalendarMap($bookings, $monthStart, $monthEnd);
        $calendarWeeks = $this->buildCalendarWeeks($monthStart, $monthEnd, $calendarMap);

        $recentBookings = VehicleBooking::with(['gallery:id,title,po_key'])
            ->latest('departure_date')
            ->latest('id')
            ->limit(30)
            ->get();

        return view('admin.bookings.index', [
            'month' => $monthStart,
            'currentMonth' => $currentMonth,
            'calendarWeeks' => $calendarWeeks,
            'bookings' => $recentBookings,
        ]);
    }

    public function create()
    {
        return view('admin.bookings.create', [
            'poOptions' => gallery_po_list(),
            'galleries' => $this->getAvailableGalleries(),
            'serviceTypes' => self::SERVICE_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBookingData($request);

        $validated['is_paid'] = (bool) ($validated['mark_as_paid'] ?? false);
        $validated['is_cancelled'] = false;
        $validated['booking_code'] = $this->generateBookingCode();

        unset($validated['mark_as_paid']);

        VehicleBooking::create($validated);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking berhasil ditambahkan.');
    }

    public function edit(VehicleBooking $vehicleBooking)
    {
        return view('admin.bookings.edit', [
            'booking' => $vehicleBooking,
            'poOptions' => gallery_po_list(),
            'galleries' => $this->getAvailableGalleries(),
            'serviceTypes' => self::SERVICE_TYPES,
        ]);
    }

    public function update(Request $request, VehicleBooking $vehicleBooking)
    {
        $validated = $this->validateBookingData($request);
        $validated['is_paid'] = (bool) ($validated['mark_as_paid'] ?? false);
        $validated['is_cancelled'] = false;

        unset($validated['mark_as_paid']);

        $vehicleBooking->update($validated);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diperbarui.');
    }

    public function togglePaid(VehicleBooking $vehicleBooking)
    {
        if ($vehicleBooking->is_cancelled) {
            return back()->with('success', 'Booking dibatalkan, aktifkan kembali untuk mengubah status lunas.');
        }

        $vehicleBooking->update([
            'is_paid' => !$vehicleBooking->is_paid,
        ]);

        return back()->with('success', $vehicleBooking->is_paid ? 'Booking ditandai lunas.' : 'Status lunas dibatalkan.');
    }

    public function toggleCancel(VehicleBooking $vehicleBooking)
    {
        $nextCancelledState = !$vehicleBooking->is_cancelled;

        $vehicleBooking->update([
            'is_cancelled' => $nextCancelledState,
            'is_paid' => $nextCancelledState ? false : $vehicleBooking->is_paid,
        ]);

        return back()->with('success', $nextCancelledState ? 'Booking berhasil dibatalkan.' : 'Booking diaktifkan kembali.');
    }

    public function invoice(VehicleBooking $vehicleBooking)
    {
        $vehicleBooking->loadMissing(['gallery:id,title,po_key']);

        return view('admin.bookings.invoice', [
            'booking' => $vehicleBooking,
        ]);
    }

    private function generateBookingCode(): string
    {
        do {
            $code = 'BK-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (VehicleBooking::where('booking_code', $code)->exists());

        return $code;
    }

    private function buildCalendarMap(Collection $bookings, Carbon $monthStart, Carbon $monthEnd): array
    {
        $map = [];

        foreach ($bookings as $booking) {
            $departureDate = Carbon::parse($booking->departure_date)->startOfDay();
            $returnDate = Carbon::parse($booking->return_date)->startOfDay();
            $start = $departureDate->lt($monthStart) ? $monthStart->copy() : $departureDate->copy();
            $end = $returnDate->gt($monthEnd) ? $monthEnd->copy() : $returnDate->copy();

            while ($start->lte($end)) {
                $key = $start->toDateString();
                $map[$key] ??= [];
                $map[$key][] = $booking;
                $start->addDay();
            }
        }

        return $map;
    }

    private function buildCalendarWeeks(Carbon $monthStart, Carbon $monthEnd, array $calendarMap): array
    {
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $pointer = $calendarStart->copy();

        $weeks = [];
        while ($pointer->lte($calendarEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $pointer->copy();
                $key = $date->toDateString();
                $week[] = [
                    'date' => $date,
                    'in_month' => $date->month === $monthStart->month,
                    'bookings' => $calendarMap[$key] ?? [],
                ];
                $pointer->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    private function getAvailableGalleries()
    {
        $galleryQuery = Gallery::query()
            ->select(['id', 'title', 'po_key'])
            ->orderBy('title');

        if (Schema::hasColumn('galleries', 'is_active')) {
            $galleryQuery->where('is_active', true);
        }

        return $galleryQuery->get();
    }

    private function validateBookingData(Request $request): array
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'departure_from' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'pickup_location' => ['required', 'string', 'max:1000'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'service_type' => ['required', Rule::in(self::SERVICE_TYPES)],
            'service_type_note' => ['nullable', 'string', 'max:255'],
            'po_key' => ['nullable', 'string', Rule::in(gallery_po_keys())],
            'gallery_id' => ['nullable', 'integer', 'exists:galleries,id'],
            'deal_price' => ['required', 'numeric', 'min:0'],
            'markup_price' => ['required', 'numeric', 'min:0'],
            'dp_amount' => ['nullable', 'numeric', 'min:0', 'lte:markup_price'],
            'owner_dp_amount' => ['nullable', 'numeric', 'min:0'],
            'mark_as_paid' => ['nullable', 'boolean'],
        ], [
            'departure_date.after_or_equal' => 'Tanggal berangkat minimal hari ini.',
            'return_date.after_or_equal' => 'Tanggal pulang harus sama atau setelah tanggal berangkat.',
            'dp_amount.lte' => 'Jumlah DP tidak boleh lebih besar dari harga ke customer.',
            'pickup_time.date_format' => 'Jam penjemputan harus menggunakan format HH:MM.',
        ]);

        if (($validated['service_type'] ?? '') !== 'DLL') {
            $validated['service_type_note'] = null;
        }

        $validated['customer_name'] = trim((string) $validated['customer_name']);
        $validated['customer_phone'] = trim((string) $validated['customer_phone']);
        $validated['departure_from'] = trim((string) $validated['departure_from']);
        $validated['destination'] = trim((string) $validated['destination']);
        $validated['pickup_location'] = trim((string) $validated['pickup_location']);
        $validated['dp_amount'] = $validated['dp_amount'] ?? 0;
        $validated['owner_dp_amount'] = $validated['owner_dp_amount'] ?? 0;

        return $validated;
    }
}
