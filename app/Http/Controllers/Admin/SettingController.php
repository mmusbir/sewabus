<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allValues();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'nullable|string',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'header_logo_text' => 'nullable|string',
            'header_logo_image' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'header_logo_image_dark' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'footer_logo_text' => 'nullable|string',
            'footer_logo_image' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'header_logo_show_text' => 'nullable|boolean',
            'footer_logo_show_text' => 'nullable|boolean',
            'footer_map_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'footer_map_url' => 'nullable|string',
            'footer_description' => 'nullable|string',
            'hero_title' => 'nullable|string',
            'hero_subtitle' => 'nullable|string',
            'hero_show_title' => 'nullable|boolean',
            'hero_show_subtitle' => 'nullable|boolean',
            'hero_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'hero_image_1' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'hero_image_2' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'hero_image_3' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'hero_carousel_interval_seconds' => 'nullable|integer|min:1|max:30',
            'catalog_pdf' => 'nullable|file|mimes:pdf|max:20480',
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
            'header_logo_image.max' => 'Logo navbar maksimal 2 MB.',
            'header_logo_image_dark.max' => 'Logo navbar mode malam maksimal 2 MB.',
            'footer_logo_image.max' => 'Logo footer maksimal 2 MB.',
            'footer_map_image.max' => 'Gambar maps maksimal 4 MB.',
            'hero_image.max' => 'Gambar hero maksimal 4 MB.',
            'hero_image_1.max' => 'Gambar hero 1 maksimal 4 MB.',
            'hero_image_2.max' => 'Gambar hero 2 maksimal 4 MB.',
            'hero_image_3.max' => 'Gambar hero 3 maksimal 4 MB.',
            'catalog_pdf.max' => 'PDF katalog maksimal 20 MB.',
            'catalog_pdf.mimes' => 'File katalog harus berformat PDF.',
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
                    $path = $file->store('settings', 'public');
                } catch (Throwable) {
                    return back()
                        ->withErrors([$key => 'Upload gagal saat menyimpan file. Coba ulangi atau kecilkan ukuran file.'])
                        ->withInput();
                }
                $value = Storage::url($path);
            }

            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        Setting::clearRuntimeCache();

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    private function deleteOldFileIfExists(?string $oldValue): void
    {
        if (!$oldValue) {
            return;
        }

        $oldPath = str_starts_with($oldValue, '/storage/')
            ? substr($oldValue, 9)
            : $oldValue;

        Storage::disk('public')->delete($oldPath);
    }
}
