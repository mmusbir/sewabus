<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalPackage;
use Illuminate\Http\Request;

class RentalPackageController extends Controller
{
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

        if ($request->hasFile('image')) {
            $validated['image_path'] = store_media($request->file('image'), 'rental-packages');
        }

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

        if ($request->hasFile('image')) {
            if ($rentalPackage->image_path) {
                delete_media($rentalPackage->getRawOriginal('image_path'));
            }

            $validated['image_path'] = store_media($request->file('image'), 'rental-packages');
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
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
            'image' => $imageRule . '|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'image.required' => 'Gambar paket wajib diunggah.',
            'image.max' => 'Gambar paket maksimal 2 MB.',
        ]);
    }
}
