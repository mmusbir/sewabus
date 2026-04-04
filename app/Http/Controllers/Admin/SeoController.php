<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Throwable;

class SeoController extends Controller
{
    public function index()
    {
        $settings = Setting::allValues();

        return view('admin.settings.seo', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'seo_meta_title_default' => 'nullable|string|max:160',
            'seo_meta_description_default' => 'nullable|string|max:320',
            'seo_meta_keywords_default' => 'nullable|string|max:500',
            'seo_og_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'seo_home_title' => 'nullable|string|max:160',
            'seo_home_description' => 'nullable|string|max:320',
            'seo_katalog_title' => 'nullable|string|max:160',
            'seo_katalog_description' => 'nullable|string|max:320',
            'seo_packages_title' => 'nullable|string|max:160',
            'seo_packages_description' => 'nullable|string|max:320',
            'seo_contact_title' => 'nullable|string|max:160',
            'seo_contact_description' => 'nullable|string|max:320',
        ], [
            'seo_og_image.max' => 'Gambar OG maksimal 4 MB.',
            'seo_og_image.mimes' => 'Gambar OG harus PNG/JPG/JPEG/WEBP.',
        ]);

        $existingSettings = Setting::allValues();

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);

                if (!$file || !$file->isValid()) {
                    return back()
                        ->withErrors([$key => 'Upload gambar OG gagal.'])
                        ->withInput();
                }

                $oldValue = $existingSettings[$key] ?? null;
                delete_media($oldValue);

                try {
                    $path = store_media($file, 'settings');
                    $value = $path;
                } catch (Throwable) {
                    return back()
                        ->withErrors([$key => 'Gagal menyimpan gambar OG.'])
                        ->withInput();
                }
            }

            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        Setting::clearRuntimeCache();

        return back()->with('success', 'Pengaturan SEO berhasil diperbarui.');
    }
}
