<?php

namespace App\Http\Controllers;

use App\Models\RentalPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $typeTabs = [
            'all' => 'Semua Paket',
            'sewa' => 'Paket Sewa',
            'liburan' => 'Paket Liburan',
        ];

        $selectedType = $request->query('type', 'all');
        if (!array_key_exists($selectedType, $typeTabs)) {
            $selectedType = 'all';
        }

        $query = RentalPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->latest();

        if ($selectedType !== 'all') {
            $query->where('type', $selectedType);
        }

        $packages = $query->paginate(9)->withQueryString();

        return view('paket', compact('packages', 'typeTabs', 'selectedType'));
    }
}
