<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToWebp extends Command
{
    protected $signature = 'media:migrate-webp {--dry-run : Simulasi tanpa mengubah file/database}';

    protected $description = 'One-time migrasi media lama (JPG/PNG) di storage menjadi WebP lalu update path di database.';

    private int $converted = 0;
    private int $skipped = 0;
    private int $failed = 0;
    private int $updatedRows = 0;

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $disk = Storage::disk(media_disk());

        if (!function_exists('imagewebp')) {
            $this->error('Fungsi imagewebp() tidak tersedia. Aktifkan ekstensi GD dengan dukungan WebP di PHP.');
            return self::FAILURE;
        }

        $this->line($dryRun
            ? 'Mode dry-run aktif. Tidak ada file/database yang diubah.'
            : 'Menjalankan migrasi media ke WebP...');

        $this->migrateGalleryMedia($disk, $dryRun);
        $this->migrateGalleriesCover($disk, $dryRun);
        $this->migrateRentalPackages($disk, $dryRun);
        $this->migrateSettings($disk, $dryRun);

        $this->newLine();
        $this->info('Selesai.');
        $this->line('Converted: '.$this->converted);
        $this->line('Updated rows: '.$this->updatedRows);
        $this->line('Skipped: '.$this->skipped);
        $this->line('Failed: '.$this->failed);

        return self::SUCCESS;
    }

    private function migrateGalleryMedia($disk, bool $dryRun): void
    {
        DB::table('gallery_media')
            ->select(['id', 'media_path', 'type'])
            ->where('type', 'image')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($disk, $dryRun): void {
                foreach ($rows as $row) {
                    $newPath = $this->convertPathToWebp((string) $row->media_path, $disk, $dryRun);
                    if (!$newPath || $newPath === $row->media_path) {
                        continue;
                    }

                    if (!$dryRun) {
                        DB::table('gallery_media')->where('id', $row->id)->update(['media_path' => $newPath]);
                    }
                    $this->updatedRows++;
                }
            });
    }

    private function migrateGalleriesCover($disk, bool $dryRun): void
    {
        DB::table('galleries')
            ->select(['id', 'image_path'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($disk, $dryRun): void {
                foreach ($rows as $row) {
                    $newPath = $this->convertPathToWebp((string) $row->image_path, $disk, $dryRun);
                    if (!$newPath || $newPath === $row->image_path) {
                        continue;
                    }

                    if (!$dryRun) {
                        DB::table('galleries')->where('id', $row->id)->update(['image_path' => $newPath]);
                    }
                    $this->updatedRows++;
                }
            });
    }

    private function migrateRentalPackages($disk, bool $dryRun): void
    {
        DB::table('rental_packages')
            ->select(['id', 'image_path'])
            ->whereNotNull('image_path')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($disk, $dryRun): void {
                foreach ($rows as $row) {
                    $newPath = $this->convertPathToWebp((string) $row->image_path, $disk, $dryRun);
                    if (!$newPath || $newPath === $row->image_path) {
                        continue;
                    }

                    if (!$dryRun) {
                        DB::table('rental_packages')->where('id', $row->id)->update(['image_path' => $newPath]);
                    }
                    $this->updatedRows++;
                }
            });
    }

    private function migrateSettings($disk, bool $dryRun): void
    {
        $mediaKeys = [
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

        DB::table('settings')
            ->select(['id', 'key', 'value'])
            ->whereIn('key', $mediaKeys)
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($disk, $dryRun): void {
                foreach ($rows as $row) {
                    $newPath = $this->convertPathToWebp((string) $row->value, $disk, $dryRun);
                    if (!$newPath || $newPath === $row->value) {
                        continue;
                    }

                    if (!$dryRun) {
                        DB::table('settings')->where('id', $row->id)->update(['value' => $newPath]);
                    }
                    $this->updatedRows++;
                }
            });
    }

    private function convertPathToWebp(string $path, $disk, bool $dryRun): ?string
    {
        $storedPath = stored_media_path($path);

        if (!is_string($storedPath) || $storedPath === '' || is_external_media_path($storedPath) || str_starts_with($storedPath, '/')) {
            $this->skipped++;
            return null;
        }

        $ext = strtolower((string) pathinfo($storedPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['webp', 'svg', 'ico'], true)) {
            $this->skipped++;
            return null;
        }

        if (!$disk->exists($storedPath)) {
            $this->failed++;
            $this->warn("File tidak ditemukan: {$storedPath}");
            return null;
        }

        $binary = $disk->get($storedPath);
        $image = @imagecreatefromstring($binary);
        if (!is_resource($image) && !($image instanceof \GdImage)) {
            $this->failed++;
            $this->warn("Gagal baca gambar: {$storedPath}");
            return null;
        }

        $newPath = preg_replace('/\.[^.]+$/', '', $storedPath).'.webp';
        if (!is_string($newPath) || $newPath === '' || $newPath === $storedPath) {
            imagedestroy($image);
            $this->skipped++;
            return null;
        }

        ob_start();
        imagewebp($image, null, 78);
        $webpBinary = ob_get_clean();
        imagedestroy($image);

        if ($webpBinary === false || $webpBinary === '') {
            $this->failed++;
            $this->warn("Gagal encode WebP: {$storedPath}");
            return null;
        }

        if (!$dryRun) {
            $disk->put($newPath, $webpBinary);
            $disk->delete($storedPath);
        }

        $this->converted++;
        $this->line(($dryRun ? '[DRY] ' : '').$storedPath.' -> '.$newPath);

        return $newPath;
    }
}

