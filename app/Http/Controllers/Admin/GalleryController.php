<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = Gallery::with('images', 'video')->latest()->get();
        return view('admin.galleries.index', compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.galleries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:minibus,mediumbus,bigbus',
            'unit_count' => 'required|integer|min:1|max:999',
            'images' => 'required|array|min:1|max:6',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:20480',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'images.required' => 'Minimal 1 foto armada wajib diunggah.',
            'images.max' => 'Maksimal 6 foto armada.',
            'images.*.max' => 'Maksimal ukuran tiap foto adalah 4 MB.',
            'video.max' => 'Maksimal ukuran video adalah 20 MB.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['image_path'] = '';

        $gallery = Gallery::create($validated);

        $this->syncGalleryMedia(
            gallery: $gallery,
            newImages: $request->file('images', []),
            videoFile: $request->file('video'),
            replaceImages: true
        );

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gallery $gallery)
    {
        $gallery->load('images', 'video');
        return view('admin.galleries.edit', compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|in:minibus,mediumbus,bigbus',
            'unit_count' => 'required|integer|min:1|max:999',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'replace_images' => 'nullable|boolean',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:20480',
            'remove_video' => 'nullable|boolean',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'images.max' => 'Maksimal 6 foto armada.',
            'images.*.max' => 'Maksimal ukuran tiap foto adalah 4 MB.',
            'video.max' => 'Maksimal ukuran video adalah 20 MB.',
        ]);

        $gallery->load('images', 'video');

        $newImages = $request->file('images', []);
        $replaceImages = (bool) $request->boolean('replace_images');
        $existingImageCount = $gallery->images->count();
        $incomingCount = is_array($newImages) ? count($newImages) : 0;

        if (!$replaceImages && ($existingImageCount + $incomingCount) > 6) {
            throw ValidationException::withMessages([
                'images' => 'Total foto melebihi batas maksimal 6. Gunakan mode ganti semua foto atau kurangi jumlah upload.',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $gallery->update($validated);

        $this->syncGalleryMedia(
            gallery: $gallery,
            newImages: is_array($newImages) ? $newImages : [],
            videoFile: $request->file('video'),
            replaceImages: $replaceImages,
            removeVideo: (bool) $request->boolean('remove_video')
        );

        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gallery $gallery)
    {
        $gallery->load('media');

        $pathsToDelete = [];
        if ($gallery->image_path) {
            $pathsToDelete[] = str_replace('/storage/', '', $gallery->image_path);
        }

        foreach ($gallery->media as $media) {
            $pathsToDelete[] = str_replace('/storage/', '', $media->media_path);
        }

        $pathsToDelete = array_values(array_unique(array_filter($pathsToDelete)));
        if (!empty($pathsToDelete)) {
            Storage::disk('public')->delete($pathsToDelete);
        }

        $gallery->media()->delete();
        
        $gallery->delete();
        
        return redirect()->route('admin.galleries.index')->with('success', 'Galeri berhasil dihapus.');
    }

    public function toggleStatus(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $gallery->update([
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('admin.galleries.index')->with('success', 'Status armada berhasil diperbarui.');
    }

    private function syncGalleryMedia(
        Gallery $gallery,
        array $newImages = [],
        mixed $videoFile = null,
        bool $replaceImages = false,
        bool $removeVideo = false
    ): void {
        $gallery->load('images', 'video');

        if ($replaceImages) {
            foreach ($gallery->images as $imageMedia) {
                $oldPath = str_replace('/storage/', '', $imageMedia->media_path);
                Storage::disk('public')->delete($oldPath);
                $imageMedia->delete();
            }
            $gallery->unsetRelation('images');
        }

        $currentImageCount = $gallery->images()->count();
        $sortOrder = $gallery->images()->max('sort_order') ?? -1;

        foreach ($newImages as $imageFile) {
            if ($currentImageCount >= 6) {
                break;
            }
            $imagePath = $imageFile->store('galleries', 'public');
            $sortOrder++;

            GalleryMedia::create([
                'gallery_id' => $gallery->id,
                'type' => 'image',
                'media_path' => '/storage/' . $imagePath,
                'sort_order' => $sortOrder,
            ]);

            $currentImageCount++;
        }

        $existingVideo = $gallery->video()->first();
        if ($removeVideo && $existingVideo) {
            $oldPath = str_replace('/storage/', '', $existingVideo->media_path);
            Storage::disk('public')->delete($oldPath);
            $existingVideo->delete();
            $existingVideo = null;
        }

        if ($videoFile) {
            if ($existingVideo) {
                $oldPath = str_replace('/storage/', '', $existingVideo->media_path);
                Storage::disk('public')->delete($oldPath);
                $existingVideo->delete();
            }

            $videoPath = $videoFile->store('galleries', 'public');
            GalleryMedia::create([
                'gallery_id' => $gallery->id,
                'type' => 'video',
                'media_path' => '/storage/' . $videoPath,
                'sort_order' => 0,
            ]);
        }

        $cover = $gallery->images()->orderBy('sort_order')->first();
        if ($cover) {
            $gallery->image_path = $cover->media_path;
            $gallery->save();
        }
    }
}
