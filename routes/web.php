<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RentalPackageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/up', function () {
    return response()->json([
        'status' => 'ok',
    ]);
});

Route::get('/sitemap.xml', function () {
    $entries = collect([
        [
            'loc' => route('home'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ],
        [
            'loc' => route('katalog.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ],
        [
            'loc' => route('packages.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ],
        [
            'loc' => route('contact.index'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ],
    ]);

    try {
        if (Schema::hasTable('galleries')) {
            $query = \App\Models\Gallery::query();
            if (Schema::hasColumn('galleries', 'is_active')) {
                $query->where('is_active', true);
            }

            $query->latest('updated_at')
                ->get(['id', 'updated_at'])
                ->each(function (\App\Models\Gallery $gallery) use ($entries) {
                    $entries->push([
                        'loc' => route('katalog.show', $gallery),
                        'lastmod' => optional($gallery->updated_at)->toAtomString() ?? now()->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8',
                    ]);
                });
        }
    } catch (\Throwable $exception) {
        report($exception);
    }

    return response()
        ->view('sitemap', ['entries' => $entries], 200)
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/', function () {
    $galleries = collect();
    $liburanPackages = collect();
    $sewaPackages = collect();
    $databaseUnavailable = false;

    try {
        if (Schema::hasTable('galleries')) {
            $query = \App\Models\Gallery::query()->latest()->take(3);
            if (Schema::hasColumn('galleries', 'is_active')) {
                $query->where('is_active', true);
            }
            $galleries = $query->get();
        }
    } catch (\Throwable $exception) {
        report($exception);
        $databaseUnavailable = true;
    }

    try {
        if (Schema::hasTable('rental_packages')) {
            $buildPackageQuery = function () {
                $query = \App\Models\RentalPackage::query()->latest()->take(3);

                if (Schema::hasColumn('rental_packages', 'is_active')) {
                    $query->where('is_active', true);
                }

                if (Schema::hasColumn('rental_packages', 'sort_order')) {
                    $query->orderBy('sort_order');
                }

                return $query;
            };

            if (Schema::hasColumn('rental_packages', 'type')) {
                $liburanPackages = $buildPackageQuery()->where('type', 'liburan')->get();
                $sewaPackages = $buildPackageQuery()->where('type', 'sewa')->get();
            } else {
                $allPackages = $buildPackageQuery()->get();
                $liburanPackages = $allPackages;
                $sewaPackages = $allPackages;
            }
        }
    } catch (\Throwable $exception) {
        report($exception);
        $databaseUnavailable = true;
    }

    return view('welcome', compact('galleries', 'liburanPackages', 'sewaPackages', 'databaseUnavailable'));
})->name('home');

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{gallery}', [KatalogController::class, 'show'])->name('katalog.show');
Route::get('/paket', [PackageController::class, 'index'])->name('packages.index');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()?->hasPanelAccess()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin/galleries', GalleryController::class)->except(['show'])->names([
        'index' => 'admin.galleries.index',
        'create' => 'admin.galleries.create',
        'store' => 'admin.galleries.store',
        'edit' => 'admin.galleries.edit',
        'update' => 'admin.galleries.update',
        'destroy' => 'admin.galleries.destroy',
    ]);
    Route::patch('/admin/galleries/{gallery}/status', [GalleryController::class, 'toggleStatus'])->name('admin.galleries.toggle-status');

    Route::resource('admin/rental-packages', RentalPackageController::class)->parameters([
        'rental-packages' => 'rentalPackage',
    ])->except(['show'])->names([
        'index' => 'admin.rental-packages.index',
        'create' => 'admin.rental-packages.create',
        'store' => 'admin.rental-packages.store',
        'edit' => 'admin.rental-packages.edit',
        'update' => 'admin.rental-packages.update',
        'destroy' => 'admin.rental-packages.destroy',
    ]);

    Route::middleware('admin.settings')->group(function () {
        Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::get('/admin/settings/categories', [SettingController::class, 'categoryIndex'])->name('admin.settings.categories.index');
        Route::post('/admin/settings/categories', [SettingController::class, 'categoryUpdate'])->name('admin.settings.categories.update');
        Route::get('/admin/settings/po', [SettingController::class, 'poIndex'])->name('admin.settings.po.index');
        Route::post('/admin/settings/po', [SettingController::class, 'poUpdate'])->name('admin.settings.po.update');
        Route::get('/admin/settings/facilities', [SettingController::class, 'facilityIndex'])->name('admin.settings.facilities.index');
        Route::post('/admin/settings/facilities', [SettingController::class, 'facilityUpdate'])->name('admin.settings.facilities.update');
        Route::get('/admin/settings/seo', [AdminSeoController::class, 'index'])->name('admin.settings.seo.index');
        Route::post('/admin/settings/seo', [AdminSeoController::class, 'update'])->name('admin.settings.seo.update');
    });

    Route::middleware('admin.users')->group(function () {
        Route::get('/admin/settings/users', [UserManagementController::class, 'index'])->name('admin.settings.users.index');
        Route::get('/admin/settings/users/create', [UserManagementController::class, 'create'])->name('admin.settings.users.create');
        Route::post('/admin/settings/users', [UserManagementController::class, 'store'])->name('admin.settings.users.store');
        Route::get('/admin/settings/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.settings.users.edit');
        Route::put('/admin/settings/users/{user}', [UserManagementController::class, 'update'])->name('admin.settings.users.update');
        Route::delete('/admin/settings/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.settings.users.destroy');
        Route::post('/admin/settings/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.settings.users.reset-password');
    });
});

require __DIR__.'/auth.php';
