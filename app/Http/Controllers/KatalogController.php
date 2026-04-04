<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $categoryConfigs = gallery_category_list();
        $categoryTabs = gallery_category_tabs();
        $facilityOptions = catalog_facilities();

        $seatOptions = collect($categoryConfigs)
            ->mapWithKeys(fn (array $category) => [
                $category['key'] => [
                    'label' => $category['description'] ?: $category['label'],
                    'categories' => [$category['key']],
                ],
            ])
            ->all();

        $selectedCategory = $request->query('category', 'all');
        if (!array_key_exists($selectedCategory, $categoryTabs)) {
            $selectedCategory = 'all';
        }

        $selectedFacilities = array_values(array_filter(
            (array) $request->query('facilities', []),
            fn ($facility) => array_key_exists($facility, $facilityOptions)
        ));

        $selectedSeats = $request->query('seats');
        if (!array_key_exists((string) $selectedSeats, $seatOptions)) {
            $selectedSeats = null;
        }

        $searchTerm = trim((string) $request->query('q', ''));

        $query = Gallery::with('images')->latest();
        if (Schema::hasColumn('galleries', 'is_active')) {
            $query->where('is_active', true);
        }

        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }

        if ($selectedSeats) {
            $query->whereIn('category', $seatOptions[$selectedSeats]['categories']);
        }

        if ($searchTerm !== '') {
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('facilities', 'like', "%{$searchTerm}%")
                    ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        foreach ($selectedFacilities as $facilityKey) {
            $keywords = $facilityOptions[$facilityKey]['keywords'];
            $query->where(function ($subQuery) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $subQuery->orWhere('facilities', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                }
            });
        }

        $galleries = $query->paginate(9)->withQueryString();

        return view('katalog', compact(
            'galleries',
            'categoryTabs',
            'facilityOptions',
            'seatOptions',
            'selectedCategory',
            'selectedFacilities',
            'selectedSeats',
            'searchTerm'
        ));
    }

    public function show(Gallery $gallery)
    {
        if (Schema::hasColumn('galleries', 'is_active')) {
            abort_if(!$gallery->is_active, 404);
        }
        $gallery->load('images', 'video');
        return view('katalog-detail', compact('gallery'));
    }
}
