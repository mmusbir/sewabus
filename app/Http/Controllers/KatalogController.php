<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Throwable;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $categoryConfigs = gallery_category_list();
        $categoryTabs = gallery_category_tabs();
        $facilityOptions = catalog_facilities();
        $databaseUnavailable = false;

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

        try {
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
        } catch (Throwable $exception) {
            report($exception);
            $databaseUnavailable = true;
            $galleries = new LengthAwarePaginator(
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

        return view('katalog', compact(
            'galleries',
            'categoryTabs',
            'facilityOptions',
            'seatOptions',
            'selectedCategory',
            'selectedFacilities',
            'selectedSeats',
            'searchTerm',
            'databaseUnavailable'
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
