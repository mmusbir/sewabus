<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allValues();

        return view('admin.settings.index', compact('settings'));
    }

    public function categoryIndex()
    {
        $categoryRows = collect(gallery_category_list())
            ->map(fn (array $category) => [
                'original_key' => $category['key'],
                'key' => $category['key'],
                'label' => $category['label'],
                'description' => $category['description'],
            ])
            ->all();

        return view('admin.settings.categories', compact('categoryRows'));
    }

    public function facilityIndex()
    {
        $facilityRows = collect(catalog_facility_list())
            ->map(fn (array $facility) => [
                'original_key' => $facility['key'],
                'key' => $facility['key'],
                'label' => $facility['label'],
                'keywords' => implode(', ', $facility['keywords']),
            ])
            ->all();

        return view('admin.settings.facilities', compact('facilityRows'));
    }

    public function poIndex()
    {
        $poRows = collect(gallery_po_list())
            ->map(fn (array $poName) => [
                'original_key' => $poName['key'],
                'key' => $poName['key'],
                'label' => $poName['label'],
                'bg_color' => $poName['bg_color'] ?? null,
                'text_color' => $poName['text_color'] ?? null,
            ])
            ->all();

        return view('admin.settings.po', compact('poRows'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'nullable|string',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg|mimetypes:image/x-icon,image/vnd.microsoft.icon,image/png,image/jpeg|max:1024',
            'header_logo_text' => 'nullable|string',
            'header_logo_image' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'header_logo_image_dark' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'footer_logo_text' => 'nullable|string',
            'footer_logo_image' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'header_logo_show_text' => 'nullable|boolean',
            'footer_logo_show_text' => 'nullable|boolean',
            'footer_map_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'footer_map_url' => 'nullable|string',
            'footer_description' => 'nullable|string',
            'hero_title' => 'nullable|string',
            'hero_subtitle' => 'nullable|string',
            'hero_show_title' => 'nullable|boolean',
            'hero_show_subtitle' => 'nullable|boolean',
            'hero_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'hero_image_1' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'hero_image_2' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'hero_image_3' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'hero_carousel_interval_seconds' => 'nullable|integer|min:1|max:30',
            'social_facebook_url' => 'nullable|string',
            'social_instagram_url' => 'nullable|string',
            'social_threads_url' => 'nullable|string',
            'social_tiktok_url' => 'nullable|string',
            'social_twitter_url' => 'nullable|string',
            'social_whatsapp_number' => ['nullable', 'regex:/^[0-9+()\\-\\s]+$/'],
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
        ], [
            'favicon.max' => 'Favicon maksimal 1 MB.',
            'favicon.mimes' => 'Favicon harus berformat ICO/PNG/JPG/JPEG.',
            'favicon.mimetypes' => 'Favicon tidak dikenali. Gunakan file ICO/PNG/JPG/JPEG yang valid.',
            'header_logo_image.max' => 'Logo navbar maksimal 2 MB.',
            'header_logo_image_dark.max' => 'Logo navbar mode malam maksimal 2 MB.',
            'footer_logo_image.max' => 'Logo footer maksimal 2 MB.',
            'footer_map_image.max' => 'Gambar maps maksimal 4 MB.',
            'hero_image.max' => 'Gambar hero maksimal 4 MB.',
            'hero_image_1.max' => 'Gambar hero 1 maksimal 4 MB.',
            'hero_image_2.max' => 'Gambar hero 2 maksimal 4 MB.',
            'hero_image_3.max' => 'Gambar hero 3 maksimal 4 MB.',
        ]);

        $existingSettings = Setting::allValues();

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);

                if (!$file || !$file->isValid()) {
                    return back()
                        ->withErrors([$key => 'Upload gagal. Pastikan file tidak melebihi batas ukuran upload PHP.'])
                        ->withInput();
                }

                $filePath = $file->getPathname();
                if (!$filePath || !is_file($filePath)) {
                    return back()
                        ->withErrors([$key => 'Upload gagal. Folder sementara upload PHP tidak bisa diakses. Cek pengaturan upload_tmp_dir dan permission.'])
                        ->withInput();
                }

                $this->deleteOldFileIfExists($existingSettings[$key] ?? null);
                try {
                    $path = store_media($file, 'settings');
                } catch (Throwable) {
                    return back()
                        ->withErrors([$key => 'Upload gagal saat menyimpan file. Coba ulangi atau kecilkan ukuran file.'])
                        ->withInput();
                }
                $value = $path;
            }

            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        Setting::clearRuntimeCache();

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    public function categoryUpdate(Request $request)
    {
        [$categoryRows, $categoryErrors] = $this->validateCategoryRequest($request);

        if ($categoryErrors !== []) {
            return back()->withErrors($categoryErrors)->withInput();
        }

        [$categoryRenameMap, $categoryChangeError] = $this->prepareGalleryCategoryChanges($categoryRows);
        if ($categoryChangeError !== null) {
            return back()->withErrors(['category_keys' => $categoryChangeError])->withInput();
        }

        Setting::updateOrCreate([
            'key' => 'gallery_categories',
        ], [
            'value' => json_encode(
                collect($categoryRows)
                    ->map(fn (array $category) => [
                        'key' => $category['key'],
                        'label' => $category['label'],
                        'description' => $category['description'],
                    ])
                    ->values()
                    ->all(),
                JSON_UNESCAPED_UNICODE
            ),
        ]);

        $this->syncGalleryCategoryKeys($categoryRenameMap);
        Setting::clearRuntimeCache();

        return back()->with('success', 'Kategori armada berhasil diperbarui!');
    }

    public function facilityUpdate(Request $request)
    {
        [$facilityRows, $facilityErrors] = $this->validateFacilityRequest($request);

        if ($facilityErrors !== []) {
            return back()->withErrors($facilityErrors)->withInput();
        }

        Setting::updateOrCreate([
            'key' => 'catalog_facilities',
        ], [
            'value' => json_encode(
                collect($facilityRows)
                    ->map(fn (array $facility) => [
                        'key' => $facility['key'],
                        'label' => $facility['label'],
                        'keywords' => $facility['keywords'],
                    ])
                    ->values()
                    ->all(),
                JSON_UNESCAPED_UNICODE
            ),
        ]);

        Setting::clearRuntimeCache();

        return back()->with('success', 'Fasilitas katalog berhasil diperbarui!');
    }

    public function poUpdate(Request $request)
    {
        [$poRows, $poErrors] = $this->validatePoRequest($request);

        if ($poErrors !== []) {
            return back()->withErrors($poErrors)->withInput();
        }

        [$poRenameMap, $poChangeError] = $this->prepareGalleryPoChanges($poRows);
        if ($poChangeError !== null) {
            return back()->withErrors(['po_keys' => $poChangeError])->withInput();
        }

        Setting::updateOrCreate([
            'key' => 'gallery_po_names',
        ], [
            'value' => json_encode(
                collect($poRows)
                    ->map(fn (array $poName) => [
                        'key' => $poName['key'],
                        'label' => $poName['label'],
                        'bg_color' => $poName['bg_color'],
                        'text_color' => $poName['text_color'],
                    ])
                    ->values()
                    ->all(),
                JSON_UNESCAPED_UNICODE
            ),
        ]);

        $this->syncGalleryPoKeys($poRenameMap);
        Setting::clearRuntimeCache();

        return back()->with('success', 'Nama PO armada berhasil diperbarui!');
    }

    private function deleteOldFileIfExists(?string $oldValue): void
    {
        delete_media($oldValue);
    }

    private function validateCategoryRequest(Request $request): array
    {
        $request->validate([
            'category_original_keys' => 'nullable|array',
            'category_original_keys.*' => 'nullable|string|max:50',
            'category_keys' => 'nullable|array',
            'category_keys.*' => 'nullable|string|max:50',
            'category_labels' => 'nullable|array',
            'category_labels.*' => 'nullable|string|max:100',
            'category_descriptions' => 'nullable|array',
            'category_descriptions.*' => 'nullable|string|max:100',
        ], [
            'category_keys.*.max' => 'Kode kategori maksimal 50 karakter.',
            'category_labels.*.max' => 'Nama kategori maksimal 100 karakter.',
            'category_descriptions.*.max' => 'Keterangan kategori maksimal 100 karakter.',
        ]);

        return $this->buildCategoryRowsFromRequest($request);
    }

    private function validateFacilityRequest(Request $request): array
    {
        $request->validate([
            'facility_original_keys' => 'nullable|array',
            'facility_original_keys.*' => 'nullable|string|max:50',
            'facility_keys' => 'nullable|array',
            'facility_keys.*' => 'nullable|string|max:50',
            'facility_labels' => 'nullable|array',
            'facility_labels.*' => 'nullable|string|max:100',
            'facility_keywords' => 'nullable|array',
            'facility_keywords.*' => 'nullable|string|max:255',
        ], [
            'facility_keys.*.max' => 'Kode fasilitas maksimal 50 karakter.',
            'facility_labels.*.max' => 'Nama fasilitas maksimal 100 karakter.',
            'facility_keywords.*.max' => 'Daftar keyword fasilitas maksimal 255 karakter.',
        ]);

        return $this->buildFacilityRowsFromRequest($request);
    }

    private function validatePoRequest(Request $request): array
    {
        $request->validate([
            'po_original_keys' => 'nullable|array',
            'po_original_keys.*' => 'nullable|string|max:50',
            'po_keys' => 'nullable|array',
            'po_keys.*' => 'nullable|string|max:50',
            'po_labels' => 'nullable|array',
            'po_labels.*' => 'nullable|string|max:100',
            'po_bg_colors' => 'nullable|array',
            'po_bg_colors.*' => ['nullable', 'string', 'regex:/^#?[A-Fa-f0-9]{6}$/'],
            'po_text_colors' => 'nullable|array',
            'po_text_colors.*' => ['nullable', 'string', 'regex:/^#?[A-Fa-f0-9]{6}$/'],
        ], [
            'po_keys.*.max' => 'Kode PO maksimal 50 karakter.',
            'po_labels.*.max' => 'Nama PO maksimal 100 karakter.',
            'po_bg_colors.*.regex' => 'Warna background PO harus berupa kode hex 6 digit, misalnya #E16A37.',
            'po_text_colors.*.regex' => 'Warna teks PO harus berupa kode hex 6 digit, misalnya #FFFFFF.',
        ]);

        return $this->buildPoRowsFromRequest($request);
    }

    private function buildCategoryRowsFromRequest(Request $request): array
    {
        $originalKeys = (array) $request->input('category_original_keys', []);
        $keys = (array) $request->input('category_keys', []);
        $labels = (array) $request->input('category_labels', []);
        $descriptions = (array) $request->input('category_descriptions', []);
        $rowCount = max(count($originalKeys), count($keys), count($labels), count($descriptions));

        $rows = [];
        $errors = [];
        $usedKeys = [];

        for ($index = 0; $index < $rowCount; $index++) {
            $originalKey = strtolower(trim((string) ($originalKeys[$index] ?? '')));
            $key = strtolower(trim((string) ($keys[$index] ?? '')));
            $label = trim((string) ($labels[$index] ?? ''));
            $description = trim((string) ($descriptions[$index] ?? ''));

            if ($originalKey === '' && $key === '' && $label === '' && $description === '') {
                continue;
            }

            if ($key === '') {
                $errors["category_keys.$index"] = 'Kode kategori wajib diisi.';
                continue;
            }

            if (!preg_match('/^[a-z0-9-]+$/', $key)) {
                $errors["category_keys.$index"] = 'Kode kategori hanya boleh huruf kecil, angka, dan tanda hubung.';
                continue;
            }

            if ($label === '') {
                $errors["category_labels.$index"] = 'Nama kategori wajib diisi.';
                continue;
            }

            if (in_array($key, $usedKeys, true)) {
                $errors["category_keys.$index"] = 'Kode kategori tidak boleh duplikat.';
                continue;
            }

            $usedKeys[] = $key;
            $rows[] = [
                'original_key' => $originalKey !== '' ? $originalKey : null,
                'key' => $key,
                'label' => $label,
                'description' => $description !== '' ? $description : null,
            ];
        }

        if ($rows === []) {
            $errors['category_keys'] = 'Minimal harus ada 1 kategori armada.';
        }

        return [$rows, $errors];
    }

    private function buildFacilityRowsFromRequest(Request $request): array
    {
        $originalKeys = (array) $request->input('facility_original_keys', []);
        $keys = (array) $request->input('facility_keys', []);
        $labels = (array) $request->input('facility_labels', []);
        $keywordStrings = (array) $request->input('facility_keywords', []);
        $rowCount = max(count($originalKeys), count($keys), count($labels), count($keywordStrings));

        $rows = [];
        $errors = [];
        $usedKeys = [];

        for ($index = 0; $index < $rowCount; $index++) {
            $originalKey = strtolower(trim((string) ($originalKeys[$index] ?? '')));
            $key = strtolower(trim((string) ($keys[$index] ?? '')));
            $label = trim((string) ($labels[$index] ?? ''));
            $keywordString = trim((string) ($keywordStrings[$index] ?? ''));

            if ($originalKey === '' && $key === '' && $label === '' && $keywordString === '') {
                continue;
            }

            if ($key === '') {
                $errors["facility_keys.$index"] = 'Kode fasilitas wajib diisi.';
                continue;
            }

            $key = normalize_catalog_facility_key($key);

            if ($key === '') {
                $errors["facility_keys.$index"] = 'Kode fasilitas hanya boleh huruf kecil, angka, tanda hubung, dan underscore.';
                continue;
            }

            if ($label === '') {
                $errors["facility_labels.$index"] = 'Nama fasilitas wajib diisi.';
                continue;
            }

            if (in_array($key, $usedKeys, true)) {
                $errors["facility_keys.$index"] = 'Kode fasilitas tidak boleh duplikat.';
                continue;
            }

            $keywords = collect(preg_split('/\s*,\s*/', $keywordString))
                ->map(fn ($keyword) => strtolower(trim((string) $keyword)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($keywords === []) {
                $errors["facility_keywords.$index"] = 'Minimal isi 1 keyword untuk filter fasilitas.';
                continue;
            }

            $usedKeys[] = $key;
            $rows[] = [
                'original_key' => $originalKey !== '' ? $originalKey : null,
                'key' => $key,
                'label' => $label,
                'keywords' => $keywords,
            ];
        }

        if ($rows === []) {
            $errors['facility_keys'] = 'Minimal harus ada 1 fasilitas katalog.';
        }

        return [$rows, $errors];
    }

    private function buildPoRowsFromRequest(Request $request): array
    {
        $originalKeys = (array) $request->input('po_original_keys', []);
        $keys = (array) $request->input('po_keys', []);
        $labels = (array) $request->input('po_labels', []);
        $backgroundColors = (array) $request->input('po_bg_colors', []);
        $textColors = (array) $request->input('po_text_colors', []);
        $rowCount = max(count($originalKeys), count($keys), count($labels), count($backgroundColors), count($textColors));

        $rows = [];
        $errors = [];
        $usedKeys = [];

        for ($index = 0; $index < $rowCount; $index++) {
            $originalKey = normalize_gallery_po_key((string) ($originalKeys[$index] ?? ''));
            $key = normalize_gallery_po_key((string) ($keys[$index] ?? ''));
            $label = trim((string) ($labels[$index] ?? ''));
            $backgroundColor = trim((string) ($backgroundColors[$index] ?? ''));
            $textColor = trim((string) ($textColors[$index] ?? ''));

            if ($originalKey === '' && $key === '' && $label === '' && $backgroundColor === '' && $textColor === '') {
                continue;
            }

            if ($key === '') {
                $errors["po_keys.$index"] = 'Kode PO wajib diisi.';
                continue;
            }

            if ($label === '') {
                $errors["po_labels.$index"] = 'Nama PO wajib diisi.';
                continue;
            }

            if (in_array($key, $usedKeys, true)) {
                $errors["po_keys.$index"] = 'Kode PO tidak boleh duplikat.';
                continue;
            }

            $usedKeys[] = $key;
            $rows[] = [
                'original_key' => $originalKey !== '' ? $originalKey : null,
                'key' => $key,
                'label' => $label,
                'bg_color' => normalize_hex_color($backgroundColor),
                'text_color' => normalize_hex_color($textColor),
            ];
        }

        if ($rows === []) {
            $errors['po_keys'] = 'Minimal harus ada 1 nama PO armada.';
        }

        return [$rows, $errors];
    }

    private function prepareGalleryCategoryChanges(array $categoryRows): array
    {
        if (!Schema::hasTable('galleries')) {
            return [[], null];
        }

        $renameMap = collect($categoryRows)
            ->filter(fn (array $row) => filled($row['original_key']) && $row['original_key'] !== $row['key'])
            ->mapWithKeys(fn (array $row) => [$row['original_key'] => $row['key']])
            ->all();

        $finalKeys = collect($categoryRows)
            ->pluck('key')
            ->values();

        $usedCategories = Gallery::query()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter(fn ($category) => filled($category))
            ->map(fn ($category) => strtolower(trim((string) $category)))
            ->values();

        $removedUsedCategories = $usedCategories
            ->reject(fn (string $category) => $finalKeys->contains($category) || array_key_exists($category, $renameMap))
            ->unique()
            ->values();

        if ($removedUsedCategories->isNotEmpty()) {
            $categoryNames = $removedUsedCategories
                ->map(fn (string $category) => gallery_category_label($category, $category))
                ->implode(', ');

            return [$renameMap, 'Kategori yang masih dipakai armada tidak bisa dihapus: '.$categoryNames.'.'];
        }

        return [$renameMap, null];
    }

    private function syncGalleryCategoryKeys(array $renameMap): void
    {
        foreach ($renameMap as $from => $to) {
            Gallery::where('category', $from)->update(['category' => $to]);
        }
    }

    private function prepareGalleryPoChanges(array $poRows): array
    {
        if (!Schema::hasTable('galleries') || !Schema::hasColumn('galleries', 'po_key')) {
            return [[], null];
        }

        $renameMap = collect($poRows)
            ->filter(fn (array $row) => filled($row['original_key']) && $row['original_key'] !== $row['key'])
            ->mapWithKeys(fn (array $row) => [$row['original_key'] => $row['key']])
            ->all();

        $finalKeys = collect($poRows)
            ->pluck('key')
            ->values();

        $usedPoKeys = Gallery::query()
            ->select('po_key')
            ->distinct()
            ->pluck('po_key')
            ->filter(fn ($poKey) => filled($poKey))
            ->map(fn ($poKey) => normalize_gallery_po_key((string) $poKey))
            ->values();

        $removedUsedPoKeys = $usedPoKeys
            ->reject(fn (string $poKey) => $finalKeys->contains($poKey) || array_key_exists($poKey, $renameMap))
            ->unique()
            ->values();

        if ($removedUsedPoKeys->isNotEmpty()) {
            $poNames = $removedUsedPoKeys
                ->map(fn (string $poKey) => gallery_po_label($poKey, $poKey))
                ->implode(', ');

            return [$renameMap, 'Nama PO yang masih dipakai armada tidak bisa dihapus: '.$poNames.'.'];
        }

        return [$renameMap, null];
    }

    private function syncGalleryPoKeys(array $renameMap): void
    {
        if ($renameMap === []) {
            return;
        }

        foreach ($renameMap as $from => $to) {
            Gallery::where('po_key', $from)->update(['po_key' => $to]);
        }
    }
}
