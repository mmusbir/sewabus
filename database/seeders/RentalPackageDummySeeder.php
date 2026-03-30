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
                'is_active' => true,
                'sort_order' => 20,
            ]
        );
    }
}
