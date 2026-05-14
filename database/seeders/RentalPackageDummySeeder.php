<?php

namespace Database\Seeders;

use App\Models\RentalPackage;
use Illuminate\Database\Seeder;

class RentalPackageDummySeeder extends Seeder
{
    public function run(): void
    {
        RentalPackage::updateOrCreate(
            ['title' => 'Dummy Paket Sewa - City Tour'],
            [
                'type' => 'sewa',
                'price_label' => 'Mulai Rp2.500.000 / hari',
                'duration' => '12 Jam',
                'description' => 'Paket sewa bus untuk kebutuhan city tour, antar jemput rombongan, dan perjalanan dalam kota.',
                'includes' => "Bus + Driver\nBBM Dalam Kota\nAir Mineral\nParkir Standar",
                'excludes' => "Makan peserta\nTiket objek wisata\nPenginapan",
                'terms_conditions' => "Harga berlaku untuk area dalam kota.\nBiaya overtime dihitung per jam.\nDP minimal 30% untuk booking.",
                'is_active' => true,
                'sort_order' => 10,
            ]
        );

        RentalPackage::updateOrCreate(
            ['title' => 'Dummy Paket Liburan - Malang 3D2N'],
            [
                'type' => 'liburan',
                'price_label' => 'Mulai Rp6.800.000 / paket',
                'duration' => '3 Hari 2 Malam',
                'description' => 'Paket liburan keluarga/komunitas ke Malang lengkap dengan itinerary populer dan armada nyaman.',
                'includes' => "Bus Pariwisata\nDriver + Tol\nHotel 2 Malam\nTiket Wisata Pilihan",
                'itinerary' => [
                    ['day' => 'Day 1', 'description' => "Keberangkatan dari Makassar.\nCity tour Batu di sore hari."],
                    ['day' => 'Day 2', 'description' => "Kunjungan Jatim Park dan Museum Angkut.\nMakan malam bersama rombongan."],
                    ['day' => 'Day 3', 'description' => "Belanja oleh-oleh.\nPerjalanan pulang."],
                ],
                'excludes' => "Makan di luar itinerary\nBiaya pribadi peserta",
                'terms_conditions' => "Harga dapat berubah saat high season.\nJadwal dapat menyesuaikan kondisi lalu lintas/cuaca.\nDP minimal 30% untuk booking.",
                'is_active' => true,
                'sort_order' => 20,
            ]
        );
    }
}
