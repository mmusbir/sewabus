<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\RentalPackage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hasGalleries = Schema::hasTable('galleries');
        $hasPackages = Schema::hasTable('rental_packages');
        $hasSettings = Schema::hasTable('settings');
        $hasUsers = Schema::hasTable('users');

        $stats = [
            'galleries_total' => $hasGalleries ? Gallery::count() : 0,
            'galleries_minibus' => $hasGalleries ? Gallery::where('category', 'minibus')->count() : 0,
            'galleries_mediumbus' => $hasGalleries ? Gallery::where('category', 'mediumbus')->count() : 0,
            'galleries_bigbus' => $hasGalleries ? Gallery::where('category', 'bigbus')->count() : 0,
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

        $settingsCompletion = 0;
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

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentGalleries' => $recentGalleries,
            'recentPackages' => $recentPackages,
            'settingsCompletion' => $settingsCompletion,
        ]);
    }
}
