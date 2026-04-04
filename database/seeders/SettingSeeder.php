<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'site_name' => 'Cahaya Bone | Bus Parawisata',
            'favicon' => '/favicon.ico',
            'header_logo_text' => 'BusPariwisata',
            'footer_logo_text' => 'BusPariwisata',
            'gallery_po_names' => json_encode([
                [
                    'key' => 'cahaya-bone',
                    'label' => 'Cahaya Bone',
                ],
            ], JSON_UNESCAPED_UNICODE),
            'footer_description' => 'Penyedia jasa transportasi bus pariwisata dengan pengalaman lebih dari 70 tahun. Mengutamakan keselamatan dan kenyamanan.',
            'hero_title' => 'Sewa Bus Pariwisata Terbaik untuk Perjalanan Anda',
            'hero_subtitle' => 'Armada modern, fasilitas lengkap, dan pengemudi profesional siap menemani perjalanan wisata keluarga atau korporat Anda ke seluruh Indonesia.',
            'hero_image' => '/stitch_img_hero.jpg',
            'social_facebook_url' => '#',
            'social_instagram_url' => '#',
            'social_twitter_url' => '#',
            'social_whatsapp_number' => '62812345678',
            'contact_address' => 'Jl. Pariwisata No. 123, Jakarta Selatan',
            'contact_phone' => '(021) 1234 5678',
            'contact_email' => 'info@buspariwisata.co.id',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
