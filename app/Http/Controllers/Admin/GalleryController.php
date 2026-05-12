<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        $categories = gallery_category_list();
        $poOptions = gallery_po_list();
        $facilityOptions = catalog_facility_list();

        return view('admin.galleries.create', compact('categories', 'poOptions', 'facilityOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'po_key' => ['required', 'string', Rule::in(gallery_po_keys())],
            'category' => ['required', 'string', Rule::in(gallery_category_keys())],
            'unit_count' => 'required|integer|min:1|max:999',
            'images' => 'required|array|min:1|max:6',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:20480',
            'description' => 'nullable|string',
            'facility_keys' => 'nullable|array',
            'facility_keys.*' => ['string', Rule::in(array_keys(catalog_facilities()))],
            'facility_custom' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'images.required' => 'Minimal 1 foto armada wajib diunggah.',
            'images.max' => 'Maksimal 6 foto armada.',
            'images.*.max' => 'Maksimal ukuran tiap foto adalah 4 MB.',
            'video.max' => 'Maksimal ukuran video adalah 20 MB.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['image_path'] = '';
        $validated['facilities'] = combine_gallery_facilities(
            (array) $request->input('facility_keys', []),
            $request->input('facility_custom')
        );
        unset($validated['facility_keys'], $validated['facility_custom']);

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
        $categories = gallery_category_list();
        $poOptions = gallery_po_list();
        $facilityOptions = catalog_facility_list();

        return view('admin.galleries.edit', compact('gallery', 'categories', 'poOptions', 'facilityOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'po_key' => ['required', 'string', Rule::in(gallery_po_keys())],
            'category' => ['required', 'string', Rule::in(gallery_category_keys())],
            'unit_count' => 'required|integer|min:1|max:999',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'replace_images' => 'nullable|boolean',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer',
            'cover_image_id' => 'nullable|integer',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm|max:20480',
            'remove_video' => 'nullable|boolean',
            'description' => 'nullable|string',
            'facility_keys' => 'nullable|array',
            'facility_keys.*' => ['string', Rule::in(array_keys(catalog_facilities()))],
            'facility_custom' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'images.max' => 'Maksimal 6 foto armada.',
            'images.*.max' => 'Maksimal ukuran tiap foto adalah 4 MB.',
            'video.max' => 'Maksimal ukuran video adalah 20 MB.',
        ]);

        $gallery->load('images', 'video');

        $newImages = $request->file('images', []);
        $replaceImages = (bool) $request->boolean('replace_images');
        $removeImageIds = collect($request->input('remove_image_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();
        $existingImageIds = $gallery->images->pluck('id');
        $invalidRemoveIds = $removeImageIds->diff($existingImageIds);
        if ($invalidRemoveIds->isNotEmpty()) {
            throw ValidationException::withMessages([
                'remove_image_ids' => 'Ada foto yang tidak valid untuk dihapus.',
            ]);
        }

        $coverImageId = $request->filled('cover_image_id') ? (int) $request->input('cover_image_id') : null;
        if ($coverImageId !== null && ! $existingImageIds->contains($coverImageId)) {
            throw ValidationException::withMessages([
                'cover_image_id' => 'Pilihan sampul galeri tidak valid.',
            ]);
        }

        $existingImageCount = $gallery->images->count();
        $incomingCount = is_array($newImages) ? count($newImages) : 0;
        $remainingExistingCount = $replaceImages ? 0 : max(0, $existingImageCount - $removeImageIds->count());
        $finalImageCount = $remainingExistingCount + $incomingCount;

        if ($finalImageCount > 6) {
            throw ValidationException::withMessages([
                'images' => 'Total foto melebihi batas maksimal 6. Gunakan mode ganti semua foto atau kurangi jumlah upload.',
            ]);
        }

        if ($finalImageCount < 1) {
            throw ValidationException::withMessages([
                'images' => 'Galeri armada minimal harus memiliki 1 foto. Pilih foto yang tetap disimpan atau upload foto baru.',
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['facilities'] = combine_gallery_facilities(
            (array) $request->input('facility_keys', []),
            $request->input('facility_custom')
        );
        unset($validated['replace_images'], $validated['remove_image_ids'], $validated['cover_image_id'], $validated['remove_video'], $validated['facility_keys'], $validated['facility_custom']);
        $gallery->update($validated);

        $this->syncGalleryMedia(
            gallery: $gallery,
            newImages: is_array($newImages) ? $newImages : [],
            videoFile: $request->file('video'),
            replaceImages: $replaceImages,
            removeVideo: (bool) $request->boolean('remove_video'),
            removeImageIds: $removeImageIds->all(),
            coverImageId: $coverImageId
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
        if ($gallery->getRawOriginal('image_path')) {
            $pathsToDelete[] = $gallery->getRawOriginal('image_path');
        }

        foreach ($gallery->media as $media) {
            $pathsToDelete[] = $media->getRawOriginal('media_path');
            $thumbnailPath = media_thumbnail_path($media->getRawOriginal('media_path'));
            if (is_string($thumbnailPath) && $thumbnailPath !== '') {
                $pathsToDelete[] = $thumbnailPath;
            }
        }

        $pathsToDelete = array_values(array_unique(array_filter($pathsToDelete)));
        if (!empty($pathsToDelete)) {
            delete_media($pathsToDelete);
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
        bool $removeVideo = false,
        array $removeImageIds = [],
        ?int $coverImageId = null
    ): void {
        $gallery->load('images', 'video');

        if ($replaceImages) {
            foreach ($gallery->images as $imageMedia) {
                $paths = [$imageMedia->getRawOriginal('media_path')];
                $thumbnailPath = media_thumbnail_path($imageMedia->getRawOriginal('media_path'));
                if (is_string($thumbnailPath) && $thumbnailPath !== '') {
                    $paths[] = $thumbnailPath;
                }
                delete_media($paths);
                $imageMedia->delete();
            }
            $gallery->unsetRelation('images');
        } elseif ($removeImageIds !== []) {
            foreach ($gallery->images->whereIn('id', $removeImageIds) as $imageMedia) {
                $paths = [$imageMedia->getRawOriginal('media_path')];
                $thumbnailPath = media_thumbnail_path($imageMedia->getRawOriginal('media_path'));
                if (is_string($thumbnailPath) && $thumbnailPath !== '') {
                    $paths[] = $thumbnailPath;
                }
                delete_media($paths);
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
            $imagePath = store_media($imageFile, 'galleries');
            $this->createImageThumbnail($imageFile, $imagePath);
            $sortOrder++;

            GalleryMedia::create([
                'gallery_id' => $gallery->id,
                'type' => 'image',
                'media_path' => $imagePath,
                'sort_order' => $sortOrder,
            ]);

            $currentImageCount++;
        }

        $existingVideo = $gallery->video()->first();
        if ($removeVideo && $existingVideo) {
            delete_media($existingVideo->getRawOriginal('media_path'));
            $existingVideo->delete();
            $existingVideo = null;
        }

        if ($videoFile) {
            if ($existingVideo) {
                delete_media($existingVideo->getRawOriginal('media_path'));
                $existingVideo->delete();
            }

            $videoPath = store_media($videoFile, 'galleries');
            GalleryMedia::create([
                'gallery_id' => $gallery->id,
                'type' => 'video',
                'media_path' => $videoPath,
                'sort_order' => 0,
            ]);
        }

        $cover = null;
        if (! $replaceImages && $coverImageId !== null && ! in_array($coverImageId, $removeImageIds, true)) {
            $cover = $gallery->images()->whereKey($coverImageId)->first();
        }

        if (! $cover) {
            $cover = $gallery->images()->orderBy('sort_order')->first();
        }

        if ($cover) {
            $coverPath = $cover->getRawOriginal('media_path');
            $coverThumbPath = media_thumbnail_path($coverPath);
            if (is_string($coverThumbPath) && $coverThumbPath !== '' && Storage::disk(media_disk())->exists($coverThumbPath)) {
                $gallery->image_path = $coverThumbPath;
            } else {
                $gallery->image_path = $coverPath;
            }
        } else {
            $gallery->image_path = '';
        }

        $gallery->save();
    }

    private function createImageThumbnail(UploadedFile $imageFile, string $storedImagePath): void
    {
        $thumbnailPath = media_thumbnail_path($storedImagePath);
        if (!is_string($thumbnailPath) || $thumbnailPath === '') {
            return;
        }

        try {
            $source = @file_get_contents($imageFile->getRealPath());
            if ($source === false) {
                return;
            }

            $image = @imagecreatefromstring($source);
            if (!is_resource($image) && !($image instanceof \GdImage)) {
                return;
            }

            $sourceWidth = imagesx($image);
            $sourceHeight = imagesy($image);
            if ($sourceWidth < 1 || $sourceHeight < 1) {
                imagedestroy($image);
                return;
            }

            $targetWidth = 640;
            $targetHeight = (int) max(1, round(($sourceHeight / $sourceWidth) * $targetWidth));

            $thumb = imagecreatetruecolor($targetWidth, $targetHeight);
            imagecopyresampled(
                $thumb,
                $image,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $sourceWidth,
                $sourceHeight
            );

            ob_start();
            imagejpeg($thumb, null, 75);
            $jpegBinary = ob_get_clean();

            imagedestroy($thumb);
            imagedestroy($image);

            if ($jpegBinary === false || $jpegBinary === '') {
                return;
            }

            Storage::disk(media_disk())->put($thumbnailPath, $jpegBinary);
        } catch (\Throwable) {
            // Skip thumbnail generation when image processing is unavailable.
        }
    }
}
