<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\RentalPackage;
use App\Models\User;
use App\Models\VehicleBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'galleries_total' => 0,
            'gallery_breakdown' => collect(gallery_category_list())
                ->map(fn (array $category) => [
                    'key' => $category['key'],
                    'label' => $category['label'],
                    'count' => 0,
                ])
                ->all(),
            'packages_total' => 0,
            'packages_active' => 0,
            'packages_liburan' => 0,
            'packages_sewa' => 0,
            'users_total' => 0,
            'bookings_year_total' => 0,
            'bookings_year_revenue' => 0,
            'bookings_year_profit' => 0,
        ];
        $recentBookings = collect();
        $recentPackages = collect();
        $upcomingBookings = collect();

        try {
            $hasGalleries = Schema::hasTable('galleries');
            $hasPackages = Schema::hasTable('rental_packages');
            $hasUsers = Schema::hasTable('users');
            $hasBookings = Schema::hasTable('vehicle_bookings');

            $galleryCategoryCounts = $hasGalleries
                ? Gallery::query()
                    ->selectRaw('category, COUNT(*) as total')
                    ->groupBy('category')
                    ->pluck('total', 'category')
                : collect();
            $galleryBreakdown = collect(gallery_category_list())
                ->map(fn (array $category) => [
                    'key' => $category['key'],
                    'label' => $category['label'],
                    'count' => (int) ($galleryCategoryCounts[$category['key']] ?? 0),
                ]);

            if ($galleryCategoryCounts instanceof \Illuminate\Support\Collection) {
                $knownKeys = $galleryBreakdown->pluck('key');

                $galleryBreakdown = $galleryBreakdown->concat(
                    $galleryCategoryCounts
                        ->reject(fn ($count, $key) => $knownKeys->contains($key))
                        ->map(fn ($count, $key) => [
                            'key' => $key,
                            'label' => gallery_category_label($key, $key),
                            'count' => (int) $count,
                        ])
                        ->values()
                );
            }

            $stats = [
                'galleries_total' => $hasGalleries ? Gallery::count() : 0,
                'gallery_breakdown' => $galleryBreakdown->all(),
                'packages_total' => $hasPackages ? RentalPackage::count() : 0,
                'packages_active' => $hasPackages ? RentalPackage::where('is_active', true)->count() : 0,
                'packages_liburan' => $hasPackages ? RentalPackage::where('type', 'liburan')->count() : 0,
                'packages_sewa' => $hasPackages ? RentalPackage::where('type', 'sewa')->count() : 0,
                'users_total' => $hasUsers ? User::count() : 0,
                'bookings_year_total' => 0,
                'bookings_year_revenue' => 0,
                'bookings_year_profit' => 0,
            ];

            $recentPackages = $hasPackages
                ? RentalPackage::latest()->take(5)->get()
                : collect();

            if ($hasBookings) {
                $recentBookings = VehicleBooking::query()
                    ->with(['gallery:id,title'])
                    ->where('is_cancelled', false)
                    ->latest('created_at')
                    ->latest('id')
                    ->limit(8)
                    ->get();

                $startOfYear = now()->startOfYear()->toDateString();
                $endOfYear = now()->endOfYear()->toDateString();

                $bookingsYearSummary = VehicleBooking::query()
                    ->whereBetween('departure_date', [$startOfYear, $endOfYear])
                    ->where('is_cancelled', false)
                    ->selectRaw('COUNT(*) as total_bookings')
                    ->selectRaw('COALESCE(SUM(markup_price), 0) as total_revenue')
                    ->selectRaw('COALESCE(SUM(markup_price - deal_price), 0) as total_profit')
                    ->first();

                $stats['bookings_year_total'] = (int) ($bookingsYearSummary->total_bookings ?? 0);
                $stats['bookings_year_revenue'] = (float) ($bookingsYearSummary->total_revenue ?? 0);
                $stats['bookings_year_profit'] = (float) ($bookingsYearSummary->total_profit ?? 0);

                $upcomingStart = Carbon::today()->toDateString();
                $upcomingEnd = Carbon::today()->addDays(7)->toDateString();

                $upcomingBookings = VehicleBooking::query()
                    ->with(['gallery:id,title'])
                    ->whereBetween('departure_date', [$upcomingStart, $upcomingEnd])
                    ->where('is_cancelled', false)
                    ->orderBy('departure_date')
                    ->orderBy('pickup_time')
                    ->limit(20)
                    ->get();
            }

        } catch (Throwable $exception) {
            report($exception);
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentPackages' => $recentPackages,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }
}
