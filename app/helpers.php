<?php

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        $value = \App\Models\Setting::getValue($key, $default);

        if (in_array($key, media_setting_keys(), true)) {
            return media_url($value, is_string($default) ? $default : null) ?? $default;
        }

        return $value;
    }
}

if (!function_exists('south_sulawesi_service_areas')) {
    function south_sulawesi_service_areas(): array
    {
        return [
            'Kota Makassar',
            'Kota Parepare',
            'Kota Palopo',
            'Kabupaten Bantaeng',
            'Kabupaten Barru',
            'Kabupaten Bone',
            'Kabupaten Bulukumba',
            'Kabupaten Enrekang',
            'Kabupaten Gowa',
            'Kabupaten Jeneponto',
            'Kabupaten Kepulauan Selayar',
            'Kabupaten Luwu',
            'Kabupaten Luwu Timur',
            'Kabupaten Luwu Utara',
            'Kabupaten Maros',
            'Kabupaten Pangkajene dan Kepulauan',
            'Kabupaten Pinrang',
            'Kabupaten Sidenreng Rappang',
            'Kabupaten Sinjai',
            'Kabupaten Soppeng',
            'Kabupaten Takalar',
            'Kabupaten Tana Toraja',
            'Kabupaten Toraja Utara',
            'Kabupaten Wajo',
        ];
    }
}

if (!function_exists('default_gallery_categories')) {
    function default_gallery_categories(): array
    {
        return [
            [
                'key' => 'minibus',
                'label' => 'Minibus',
                'description' => '< 20 Kursi',
            ],
            [
                'key' => 'mediumbus',
                'label' => 'Mediumbus',
                'description' => '20 - 40 Kursi',
            ],
            [
                'key' => 'bigbus',
                'label' => 'Bigbus',
                'description' => '> 40 Kursi',
            ],
        ];
    }
}

if (!function_exists('gallery_category_badge_palette')) {
    function gallery_category_badge_palette(): array
    {
        return [
            'bg-emerald-600',
            'bg-amber-500',
            'bg-rose-600',
            'bg-sky-600',
            'bg-fuchsia-600',
            'bg-secondary-green',
        ];
    }
}

if (!function_exists('gallery_category_list')) {
    function gallery_category_list(): array
    {
        $rawCategories = \App\Models\Setting::getValue('gallery_categories');

        if (is_string($rawCategories) && trim($rawCategories) !== '') {
            $decoded = json_decode($rawCategories, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $rawCategories = $decoded;
            }
        }

        if (!is_array($rawCategories) || $rawCategories === []) {
            $rawCategories = default_gallery_categories();
        }

        $categories = [];
        $usedKeys = [];
        $badgePalette = gallery_category_badge_palette();

        foreach ($rawCategories as $index => $category) {
            if (!is_array($category)) {
                continue;
            }

            $key = strtolower(trim((string) ($category['key'] ?? '')));
            $label = trim((string) ($category['label'] ?? ''));
            $description = trim((string) ($category['description'] ?? ''));

            if ($key === '' || $label === '' || in_array($key, $usedKeys, true)) {
                continue;
            }

            $usedKeys[] = $key;
            $categories[] = [
                'key' => $key,
                'label' => $label,
                'description' => $description !== '' ? $description : null,
                'badge_class' => $badgePalette[$index % count($badgePalette)] ?? 'bg-secondary-green',
            ];
        }

        if ($categories !== []) {
            return $categories;
        }

        return collect(default_gallery_categories())
            ->map(function (array $category, int $index) use ($badgePalette) {
                $category['badge_class'] = $badgePalette[$index % count($badgePalette)] ?? 'bg-secondary-green';

                return $category;
            })
            ->all();
    }
}

if (!function_exists('gallery_categories')) {
    function gallery_categories(): array
    {
        return collect(gallery_category_list())
            ->mapWithKeys(fn (array $category) => [$category['key'] => $category])
            ->all();
    }
}

if (!function_exists('gallery_category_keys')) {
    function gallery_category_keys(): array
    {
        return array_keys(gallery_categories());
    }
}

if (!function_exists('gallery_category_tabs')) {
    function gallery_category_tabs(): array
    {
        return ['all' => 'Semua'] + collect(gallery_category_list())
            ->mapWithKeys(fn (array $category) => [$category['key'] => $category['label']])
            ->all();
    }
}

if (!function_exists('gallery_category_label')) {
    function gallery_category_label(?string $key, ?string $fallback = null): string
    {
        $category = gallery_categories()[$key ?? ''] ?? null;

        if (is_array($category) && filled($category['label'] ?? null)) {
            return (string) $category['label'];
        }

        if (filled($fallback)) {
            return (string) $fallback;
        }

        return ucfirst(str_replace('-', ' ', (string) $key));
    }
}

if (!function_exists('gallery_category_description')) {
    function gallery_category_description(?string $key): ?string
    {
        $category = gallery_categories()[$key ?? ''] ?? null;

        if (!is_array($category)) {
            return null;
        }

        $description = trim((string) ($category['description'] ?? ''));

        return $description !== '' ? $description : null;
    }
}

if (!function_exists('gallery_category_full_label')) {
    function gallery_category_full_label(?string $key, ?string $fallback = null): string
    {
        $label = gallery_category_label($key, $fallback);
        $description = gallery_category_description($key);

        return $description ? sprintf('%s (%s)', $label, $description) : $label;
    }
}

if (!function_exists('gallery_category_badge_class')) {
    function gallery_category_badge_class(?string $key, string $fallback = 'bg-secondary-green'): string
    {
        $category = gallery_categories()[$key ?? ''] ?? null;

        if (is_array($category) && filled($category['badge_class'] ?? null)) {
            return (string) $category['badge_class'];
        }

        return $fallback;
    }
}

if (!function_exists('default_gallery_po_names')) {
    function default_gallery_po_names(): array
    {
        return [
            [
                'key' => 'cahaya-bone',
                'label' => 'Cahaya Bone',
                'bg_color' => '#E16A37',
                'text_color' => '#FFFFFF',
            ],
        ];
    }
}

if (!function_exists('normalize_gallery_po_key')) {
    function normalize_gallery_po_key(?string $key): string
    {
        $key = strtolower(trim((string) $key));
        $key = str_replace('_', '-', $key);
        $key = preg_replace('/[^a-z0-9-]+/', '-', $key) ?? '';
        $key = preg_replace('/-+/', '-', $key) ?? '';

        return trim($key, '-');
    }
}

if (!function_exists('normalize_hex_color')) {
    function normalize_hex_color(?string $color): ?string
    {
        $color = strtoupper(trim((string) $color));

        if ($color === '') {
            return null;
        }

        if (($color[0] ?? '') !== '#') {
            $color = '#' . ltrim($color, '#');
        }

        if (!preg_match('/^#[0-9A-F]{6}$/', $color)) {
            return null;
        }

        return $color;
    }
}

if (!function_exists('gallery_po_list')) {
    function gallery_po_list(): array
    {
        $rawPoNames = \App\Models\Setting::getValue('gallery_po_names');
        $defaultPoNames = collect(default_gallery_po_names())
            ->mapWithKeys(function (array $poName) {
                $key = normalize_gallery_po_key((string) ($poName['key'] ?? ''));

                if ($key === '') {
                    return [];
                }

                return [$key => [
                    'label' => trim((string) ($poName['label'] ?? '')),
                    'bg_color' => normalize_hex_color($poName['bg_color'] ?? null),
                    'text_color' => normalize_hex_color($poName['text_color'] ?? null),
                ]];
            })
            ->all();

        if (is_string($rawPoNames) && trim($rawPoNames) !== '') {
            $decoded = json_decode($rawPoNames, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $rawPoNames = $decoded;
            }
        }

        if (!is_array($rawPoNames) || $rawPoNames === []) {
            $rawPoNames = default_gallery_po_names();
        }

        $poNames = [];
        $usedKeys = [];

        foreach ($rawPoNames as $poName) {
            if (!is_array($poName)) {
                continue;
            }

            $key = normalize_gallery_po_key((string) ($poName['key'] ?? ''));
            $label = trim((string) ($poName['label'] ?? ''));
            $defaultPo = $defaultPoNames[$key] ?? [];

            if ($key === '' || $label === '' || in_array($key, $usedKeys, true)) {
                continue;
            }

            $usedKeys[] = $key;
            $poNames[] = [
                'key' => $key,
                'label' => $label,
                'bg_color' => normalize_hex_color($poName['bg_color'] ?? null) ?? ($defaultPo['bg_color'] ?? null),
                'text_color' => normalize_hex_color($poName['text_color'] ?? null) ?? ($defaultPo['text_color'] ?? null),
            ];
        }

        return $poNames !== [] ? $poNames : default_gallery_po_names();
    }
}

if (!function_exists('gallery_pos')) {
    function gallery_pos(): array
    {
        return collect(gallery_po_list())
            ->mapWithKeys(fn (array $poName) => [$poName['key'] => $poName])
            ->all();
    }
}

if (!function_exists('gallery_po_keys')) {
    function gallery_po_keys(): array
    {
        return array_keys(gallery_pos());
    }
}

if (!function_exists('gallery_po_label')) {
    function gallery_po_label(?string $key, ?string $fallback = null): string
    {
        $poName = gallery_pos()[$key ?? ''] ?? null;

        if (is_array($poName) && filled($poName['label'] ?? null)) {
            return (string) $poName['label'];
        }

        if (filled($fallback)) {
            return (string) $fallback;
        }

        return ucfirst(str_replace('-', ' ', (string) $key));
    }
}

if (!function_exists('gallery_po_badge_colors')) {
    function gallery_po_badge_colors(): array
    {
        $colors = [];

        foreach (gallery_po_list() as $poName) {
            $colors[$poName['key']] = [
                'bg' => normalize_hex_color($poName['bg_color'] ?? null),
                'text' => normalize_hex_color($poName['text_color'] ?? null),
            ];
        }

        return $colors;
    }
}

if (!function_exists('gallery_po_badge_style')) {
    function gallery_po_badge_style(?string $key, string $fallbackBackground = '#334155', string $fallbackText = '#FFFFFF'): string
    {
        $normalizedKey = normalize_gallery_po_key($key);
        $colors = gallery_po_badge_colors()[$normalizedKey] ?? null;
        $background = trim((string) ($colors['bg'] ?? $fallbackBackground));
        $text = trim((string) ($colors['text'] ?? $fallbackText));

        return sprintf('background-color: %s; color: %s;', $background, $text);
    }
}

if (!function_exists('default_catalog_facilities')) {
    function default_catalog_facilities(): array
    {
        return [
            [
                'key' => 'ac-wifi',
                'label' => 'AC & WiFi',
                'keywords' => ['ac', 'wifi'],
            ],
            [
                'key' => 'toilet',
                'label' => 'Toilet',
                'keywords' => ['toilet'],
            ],
            [
                'key' => 'entertainment-tv',
                'label' => 'Entertainment (TV)',
                'keywords' => ['tv', 'entertainment', 'karaoke'],
            ],
            [
                'key' => 'smoking-area',
                'label' => 'Smoking Area',
                'keywords' => ['smoking', 'merokok'],
            ],
        ];
    }
}

if (!function_exists('normalize_catalog_facility_key')) {
    function normalize_catalog_facility_key(?string $key): string
    {
        $key = strtolower(trim((string) $key));
        $key = str_replace('_', '-', $key);
        $key = preg_replace('/[^a-z0-9-]+/', '-', $key) ?? '';
        $key = preg_replace('/-+/', '-', $key) ?? '';

        return trim($key, '-');
    }
}

if (!function_exists('catalog_facility_list')) {
    function catalog_facility_list(): array
    {
        $rawFacilities = \App\Models\Setting::getValue('catalog_facilities');

        if (is_string($rawFacilities) && trim($rawFacilities) !== '') {
            $decoded = json_decode($rawFacilities, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $rawFacilities = $decoded;
            }
        }

        if (!is_array($rawFacilities) || $rawFacilities === []) {
            $rawFacilities = default_catalog_facilities();
        }

        $facilities = [];
        $usedKeys = [];

        foreach ($rawFacilities as $facility) {
            if (!is_array($facility)) {
                continue;
            }

            $key = normalize_catalog_facility_key((string) ($facility['key'] ?? ''));
            $label = trim((string) ($facility['label'] ?? ''));
            $keywords = collect((array) ($facility['keywords'] ?? []))
                ->map(fn ($keyword) => strtolower(trim((string) $keyword)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($key === '' || $label === '' || $keywords === [] || in_array($key, $usedKeys, true)) {
                continue;
            }

            $usedKeys[] = $key;
            $facilities[] = [
                'key' => $key,
                'label' => $label,
                'keywords' => $keywords,
            ];
        }

        return $facilities !== [] ? $facilities : default_catalog_facilities();
    }
}

if (!function_exists('catalog_facilities')) {
    function catalog_facilities(): array
    {
        return collect(catalog_facility_list())
            ->mapWithKeys(fn (array $facility) => [$facility['key'] => $facility])
            ->all();
    }
}

if (!function_exists('gallery_facility_lines')) {
    function gallery_facility_lines(?string $facilities): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $facilities))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

if (!function_exists('catalog_facility_matches_text')) {
    function catalog_facility_matches_text(string $text, array $facility): bool
    {
        $normalizedText = strtolower(trim($text));

        if ($normalizedText === '') {
            return false;
        }

        $label = strtolower(trim((string) ($facility['label'] ?? '')));
        if ($label !== '' && $normalizedText === $label) {
            return true;
        }

        foreach ((array) ($facility['keywords'] ?? []) as $keyword) {
            $keyword = strtolower(trim((string) $keyword));

            if ($keyword !== '' && str_contains($normalizedText, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('gallery_selected_catalog_facility_keys')) {
    function gallery_selected_catalog_facility_keys(?string $facilities): array
    {
        $facilityLines = gallery_facility_lines($facilities);

        return collect(catalog_facility_list())
            ->filter(function (array $facility) use ($facilityLines) {
                foreach ($facilityLines as $line) {
                    if (catalog_facility_matches_text($line, $facility)) {
                        return true;
                    }
                }

                return false;
            })
            ->pluck('key')
            ->values()
            ->all();
    }
}

if (!function_exists('gallery_custom_facility_lines')) {
    function gallery_custom_facility_lines(?string $facilities): array
    {
        $labels = collect(catalog_facility_list())
            ->pluck('label')
            ->map(fn ($label) => strtolower(trim((string) $label)))
            ->filter()
            ->all();

        return collect(gallery_facility_lines($facilities))
            ->reject(fn ($line) => in_array(strtolower(trim((string) $line)), $labels, true))
            ->values()
            ->all();
    }
}

if (!function_exists('gallery_custom_facility_text')) {
    function gallery_custom_facility_text(?string $facilities): string
    {
        return implode("\n", gallery_custom_facility_lines($facilities));
    }
}

if (!function_exists('combine_gallery_facilities')) {
    function combine_gallery_facilities(array $selectedFacilityKeys, ?string $customFacilities = null): ?string
    {
        $facilityMap = catalog_facilities();
        $customLines = gallery_facility_lines($customFacilities);
        $usedCustomIndexes = [];
        $resultLines = [];

        foreach ($selectedFacilityKeys as $facilityKey) {
            $facility = $facilityMap[$facilityKey] ?? null;

            if (!is_array($facility)) {
                continue;
            }

            $matchedIndex = null;
            foreach ($customLines as $index => $line) {
                if (catalog_facility_matches_text($line, $facility)) {
                    $matchedIndex = $index;
                    break;
                }
            }

            if ($matchedIndex !== null) {
                $usedCustomIndexes[] = $matchedIndex;
                $resultLines[] = $customLines[$matchedIndex];
                continue;
            }

            $label = trim((string) ($facility['label'] ?? ''));
            if ($label !== '') {
                $resultLines[] = $label;
            }
        }

        foreach ($customLines as $index => $line) {
            if (!in_array($index, $usedCustomIndexes, true)) {
                $resultLines[] = $line;
            }
        }

        $resultLines = collect($resultLines)
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $resultLines !== [] ? implode("\n", $resultLines) : null;
    }
}

if (!function_exists('media_setting_keys')) {
    function media_setting_keys(): array
    {
        return [
            'favicon',
            'header_logo_image',
            'header_logo_image_dark',
            'footer_logo_image',
            'footer_map_image',
            'hero_image',
            'hero_image_1',
            'hero_image_2',
            'hero_image_3',
            'seo_og_image',
        ];
    }
}

if (!function_exists('media_disk')) {
    function media_disk(): string
    {
        return config('filesystems.media_disk', 'public');
    }
}

if (!function_exists('is_external_media_path')) {
    function is_external_media_path(?string $path): bool
    {
        if (!is_string($path) || trim($path) === '') {
            return false;
        }

        return (bool) preg_match('#^(?:https?:)?//#i', $path)
            || str_starts_with($path, 'data:');
    }
}

if (!function_exists('stored_media_path')) {
    function stored_media_path($path): ?string
    {
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (is_external_media_path($path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, 9), '/');
        }

        try {
            $diskRootUrl = rtrim((string) \Illuminate\Support\Facades\Storage::disk(media_disk())->url('/'), '/');

            if ($diskRootUrl !== '' && str_starts_with($path, $diskRootUrl.'/')) {
                return ltrim(substr($path, strlen($diskRootUrl)), '/');
            }
        } catch (\Throwable) {
            // Ignore URL resolution failures and keep the original path.
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return ltrim($path, '/');
    }
}

if (!function_exists('media_url')) {
    function media_url($path, ?string $default = null): ?string
    {
        if (!is_string($path) || trim($path) === '') {
            return $default;
        }

        $path = trim($path);

        if (is_external_media_path($path)) {
            return $path;
        }

        if (str_starts_with($path, '/') && !str_starts_with($path, '/storage/')) {
            return $path;
        }

        $storedPath = stored_media_path($path);

        if (!is_string($storedPath) || $storedPath === '' || is_external_media_path($storedPath)) {
            return $default;
        }

        if (str_starts_with($storedPath, '/')) {
            return $storedPath;
        }

        return \Illuminate\Support\Facades\Storage::disk(media_disk())->url($storedPath);
    }
}

if (!function_exists('media_thumbnail_path')) {
    function media_thumbnail_path(?string $path): ?string
    {
        $storedPath = stored_media_path($path);

        if (!is_string($storedPath) || $storedPath === '' || is_external_media_path($storedPath) || str_starts_with($storedPath, '/')) {
            return null;
        }

        $extension = pathinfo($storedPath, PATHINFO_EXTENSION);
        $base = $extension !== ''
            ? substr($storedPath, 0, -strlen($extension) - 1)
            : $storedPath;

        return $base.'_thumb.jpg';
    }
}

if (!function_exists('supabase_render_url')) {
    function supabase_render_url(string $url, int $width, int $quality): ?string
    {
        if (!str_contains($url, '/storage/v1/object/public/')) {
            return null;
        }

        $renderUrl = str_replace('/storage/v1/object/public/', '/storage/v1/render/image/public/', $url);
        $parts = parse_url($renderUrl);

        if (!is_array($parts) || empty($parts['host']) || empty($parts['path'])) {
            return null;
        }

        $query = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['width'] = $width;
        $query['quality'] = $quality;
        $query['resize'] = $query['resize'] ?? 'cover';
        $query['format'] = $query['format'] ?? 'webp';

        $base = ($parts['scheme'] ?? 'https').'://'.$parts['host'];
        if (isset($parts['port'])) {
            $base .= ':'.$parts['port'];
        }

        return $base.$parts['path'].'?'.http_build_query($query);
    }
}

if (!function_exists('supabase_render_probe_status')) {
    function supabase_render_probe_status(string $url, bool $headOnly = true): int
    {
        if (!function_exists('curl_init')) {
            return 0;
        }

        $curl = curl_init($url);
        if ($curl === false) {
            return 0;
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'sewabus-media-probe/1.0',
        ];

        if ($headOnly) {
            $options[CURLOPT_NOBODY] = true;
        } else {
            $options[CURLOPT_RANGE] = '0-0';
        }

        curl_setopt_array($curl, $options);
        curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $status;
    }
}

if (!function_exists('supabase_render_is_supported')) {
    function supabase_render_is_supported(string $renderUrl): bool
    {
        $parts = parse_url($renderUrl);
        $host = strtolower(trim((string) ($parts['host'] ?? '')));

        if ($host === '') {
            return false;
        }

        static $runtimeCache = [];
        if (array_key_exists($host, $runtimeCache)) {
            return $runtimeCache[$host];
        }

        $cacheKey = 'media:supabase_render_supported:'.md5($host);
        $ttlMinutes = max(5, (int) config('filesystems.supabase_image_render_probe_cache_minutes', 720));

        $probe = static function () use ($renderUrl): bool {
            $status = supabase_render_probe_status($renderUrl, true);

            // Some gateways reject HEAD but allow GET.
            if ($status === 405) {
                $status = supabase_render_probe_status($renderUrl, false);
            }

            return $status >= 200 && $status < 400;
        };

        try {
            $runtimeCache[$host] = (bool) \Illuminate\Support\Facades\Cache::remember(
                $cacheKey,
                now()->addMinutes($ttlMinutes),
                $probe
            );

            return $runtimeCache[$host];
        } catch (\Throwable) {
            $runtimeCache[$host] = $probe();

            return $runtimeCache[$host];
        }
    }
}

if (!function_exists('media_thumbnail_url')) {
    function media_thumbnail_url($path, int $width = 640, int $quality = 75): ?string
    {
        $url = media_url($path);

        if (!is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);
        $width = max(1, min(2500, $width));
        $quality = max(20, min(100, $quality));

        $thumbnailPath = media_thumbnail_path($path);
        if (is_string($thumbnailPath) && $thumbnailPath !== '') {
            try {
                $disk = \Illuminate\Support\Facades\Storage::disk(media_disk());

                if ($disk->exists($thumbnailPath)) {
                    return $disk->url($thumbnailPath);
                }
            } catch (\Throwable) {
                // Keep original URL flow if thumbnail file checks fail.
            }
        }

        // Supabase image transform endpoint.
        if (str_contains($url, '/storage/v1/object/public/')) {
            if (!config('filesystems.supabase_image_render', false)) {
                return $url;
            }

            $renderUrl = supabase_render_url($url, $width, $quality);
            if (!is_string($renderUrl) || $renderUrl === '') {
                return $url;
            }

            // Safe fallback: if render endpoint is unavailable (for example 403),
            // keep using public object URL so images remain visible.
            return supabase_render_is_supported($renderUrl) ? $renderUrl : $url;
        }

        return $url;
    }
}

if (!function_exists('store_media')) {
    function store_media($file, string $directory): string
    {
        if (!($file instanceof \Illuminate\Http\UploadedFile)) {
            return $file->store($directory, media_disk());
        }

        $mime = strtolower((string) $file->getMimeType());
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $isRasterImage = str_starts_with($mime, 'image/')
            && !in_array($extension, ['svg', 'ico'], true)
            && !str_contains($mime, 'svg');

        if (!$isRasterImage) {
            return $file->store($directory, media_disk());
        }

        try {
            $source = @file_get_contents($file->getRealPath());
            if ($source === false) {
                return $file->store($directory, media_disk());
            }

            $image = @imagecreatefromstring($source);
            if (!is_resource($image) && !($image instanceof \GdImage)) {
                return $file->store($directory, media_disk());
            }

            ob_start();
            imagewebp($image, null, 78);
            $binary = ob_get_clean();
            imagedestroy($image);

            if ($binary === false || $binary === '') {
                return $file->store($directory, media_disk());
            }

            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME).'.webp';
            $path = trim($directory, '/').'/'.$filename;
            \Illuminate\Support\Facades\Storage::disk(media_disk())->put($path, $binary);

            return $path;
        } catch (\Throwable) {
            return $file->store($directory, media_disk());
        }
    }
}

if (!function_exists('delete_media')) {
    function delete_media($paths): void
    {
        $paths = is_array($paths) ? $paths : [$paths];

        $storedPaths = array_values(array_filter(array_map(function ($path) {
            $storedPath = stored_media_path($path);

            if (!is_string($storedPath) || $storedPath === '' || is_external_media_path($storedPath) || str_starts_with($storedPath, '/')) {
                return null;
            }

            return $storedPath;
        }, $paths)));

        if ($storedPaths === []) {
            return;
        }

        \Illuminate\Support\Facades\Storage::disk(media_disk())->delete($storedPaths);
    }
}
