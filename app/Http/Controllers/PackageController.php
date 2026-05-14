<?php

namespace App\Http\Controllers;

use App\Models\RentalPackage;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $typeTabs = [
            'all' => 'Semua Paket',
            'sewa' => 'Paket Sewa',
            'liburan' => 'Paket Liburan',
        ];
        $databaseUnavailable = false;

        $selectedType = $request->query('type', 'all');
        if (!array_key_exists($selectedType, $typeTabs)) {
            $selectedType = 'all';
        }

        try {
            $query = RentalPackage::where('is_active', true)
                ->orderBy('sort_order')
                ->latest();

            if ($selectedType !== 'all') {
                $query->where('type', $selectedType);
            }

            $packages = $query->paginate(9)->withQueryString();
        } catch (Throwable $exception) {
            report($exception);
            $databaseUnavailable = true;
            $packages = new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: 9,
                currentPage: max(1, (int) $request->query('page', 1)),
                options: [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        }

        return view('paket', compact('packages', 'typeTabs', 'selectedType', 'databaseUnavailable'));
    }

    public function show(RentalPackage $rentalPackage)
    {
        if (!$rentalPackage->is_active) {
            abort(404);
        }

        $relatedPackages = RentalPackage::query()
            ->where('is_active', true)
            ->where('type', $rentalPackage->type)
            ->whereKeyNot($rentalPackage->id)
            ->orderBy('sort_order')
            ->latest()
            ->take(3)
            ->get();

        return view('paket-detail', [
            'package' => $rentalPackage,
            'relatedPackages' => $relatedPackages,
        ]);
    }
}
