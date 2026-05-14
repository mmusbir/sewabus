<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalPackage;
use Illuminate\Http\Request;

class RentalPackageController extends Controller
{
    private const LIBURAN_MEDIA_UPLOAD_MAP = [
        'vehicle_exterior_image' => 'vehicle_exterior_image_path',
        'vehicle_interior_image' => 'vehicle_interior_image_path',
        'lodging_exterior_image' => 'lodging_exterior_image_path',
        'lodging_interior_image' => 'lodging_interior_image_path',
    ];

    public function index()
    {
        $packages = RentalPackage::orderBy('sort_order')->latest()->get();

        return view('admin.rental-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.rental-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated = $this->sanitizeValidatedData($validated);

        if ($request->hasFile('image')) {
            $validated['image_path'] = store_media($request->file('image'), 'rental-packages');
        }

        $this->handleLiburanMediaUploads($request, $validated);

        RentalPackage::create($validated);

        return redirect()->route('admin.rental-packages.index')
            ->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(RentalPackage $rentalPackage)
    {
        return view('admin.rental-packages.edit', compact('rentalPackage'));
    }

    public function update(Request $request, RentalPackage $rentalPackage)
    {
        $validated = $this->validateData($request, true);
        $validated = $this->sanitizeValidatedData($validated);

        if ($request->hasFile('image')) {
            if ($rentalPackage->image_path) {
                delete_media($rentalPackage->getRawOriginal('image_path'));
            }

            $validated['image_path'] = store_media($request->file('image'), 'rental-packages');
        }

        $this->handleLiburanMediaUploads($request, $validated, $rentalPackage);

        if (($validated['type'] ?? $rentalPackage->type) !== 'liburan') {
            $this->removeLiburanMedia($rentalPackage);
            foreach (self::LIBURAN_MEDIA_UPLOAD_MAP as $column) {
                $validated[$column] = null;
            }
        }

        $rentalPackage->update($validated);

        return redirect()->route('admin.rental-packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(RentalPackage $rentalPackage)
    {
        if ($rentalPackage->image_path) {
            delete_media($rentalPackage->getRawOriginal('image_path'));
        }

        $this->removeLiburanMedia($rentalPackage);

        $rentalPackage->delete();

        return redirect()->route('admin.rental-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function validateData(Request $request, bool $isUpdate = false): array
    {
        $imageRule = $isUpdate ? 'nullable' : 'required';

        return $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:sewa,liburan',
            'price_label' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'includes' => 'nullable|string',
            'itinerary' => 'nullable|array',
            'itinerary.*.day' => 'nullable|string|max:60',
            'itinerary.*.description' => 'nullable|string',
            'excludes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
            'image' => $imageRule . '|image|mimes:jpeg,png,jpg,webp|max:2048',
            'vehicle_exterior_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'vehicle_interior_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'lodging_exterior_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'lodging_interior_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'image.required' => 'Gambar paket wajib diunggah.',
            'image.max' => 'Gambar paket maksimal 2 MB.',
            'vehicle_exterior_image.max' => 'Foto luar unit maksimal 3 MB.',
            'vehicle_interior_image.max' => 'Foto dalam unit maksimal 3 MB.',
            'lodging_exterior_image.max' => 'Foto luar penginapan maksimal 3 MB.',
            'lodging_interior_image.max' => 'Foto dalam penginapan maksimal 3 MB.',
        ]);
    }

    private function sanitizeValidatedData(array $validated): array
    {
        $itinerary = collect($validated['itinerary'] ?? [])
            ->map(function ($item) {
                $day = trim((string) data_get($item, 'day', ''));
                $description = trim((string) data_get($item, 'description', ''));

                return [
                    'day' => $day,
                    'description' => $description,
                ];
            })
            ->filter(fn (array $item) => $item['day'] !== '' || $item['description'] !== '')
            ->values()
            ->all();

        $validated['itinerary'] = $itinerary !== [] ? $itinerary : null;

        foreach (['description', 'includes', 'excludes', 'terms_conditions'] as $field) {
            $value = trim((string) ($validated[$field] ?? ''));
            $validated[$field] = $value !== '' ? $value : null;
        }

        return $validated;
    }

    private function handleLiburanMediaUploads(Request $request, array &$validated, ?RentalPackage $existingPackage = null): void
    {
        if (($validated['type'] ?? $existingPackage?->type) !== 'liburan') {
            return;
        }

        foreach (self::LIBURAN_MEDIA_UPLOAD_MAP as $uploadField => $column) {
            if (!$request->hasFile($uploadField)) {
                continue;
            }

            if ($existingPackage && filled($existingPackage->getRawOriginal($column))) {
                delete_media($existingPackage->getRawOriginal($column));
            }

            $validated[$column] = store_media($request->file($uploadField), 'rental-packages/liburan');
        }
    }

    private function removeLiburanMedia(RentalPackage $rentalPackage): void
    {
        $paths = [];

        foreach (self::LIBURAN_MEDIA_UPLOAD_MAP as $column) {
            $rawPath = $rentalPackage->getRawOriginal($column);
            if (filled($rawPath)) {
                $paths[] = $rawPath;
            }
        }

        if ($paths !== []) {
            delete_media($paths);
        }
    }
}
