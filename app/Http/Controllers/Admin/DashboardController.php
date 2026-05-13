<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\RentalPackage;
use App\Models\Setting;
use App\Models\User;
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
        ];
        $recentGalleries = collect();
        $recentPackages = collect();
        $settingsCompletion = 0;

        try {
            $hasGalleries = Schema::hasTable('galleries');
            $hasPackages = Schema::hasTable('rental_packages');
            $hasSettings = Schema::hasTable('settings');
            $hasUsers = Schema::hasTable('users');

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
            ];

            $recentGalleries = $hasGalleries
                ? Gallery::latest()->take(5)->get()
                : collect();

            $recentPackages = $hasPackages
                ? RentalPackage::latest()->take(5)->get()
                : collect();

            if ($hasSettings) {
                $importantKeys = [
                    'site_name',
                    'header_logo_image',
                    'footer_logo_image',
                    'hero_title',
                    'hero_image_1',
                    'contact_phone',
                    'social_whatsapp_number',
                    'footer_map_url',
                ];

                $filled = collect($importantKeys)
                    ->filter(fn (string $key) => filled(Setting::getValue($key)))
                    ->count();

                $settingsCompletion = (int) round(($filled / count($importantKeys)) * 100);
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentGalleries' => $recentGalleries,
            'recentPackages' => $recentPackages,
            'settingsCompletion' => $settingsCompletion,
        ]);
    }
}
