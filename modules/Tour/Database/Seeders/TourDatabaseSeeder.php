<?php

namespace Modules\Tour\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tour\Models\TourLabel;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\TourDetail;
use Modules\Tour\Models\TourAvailability;
use Modules\Tour\Models\TourPhoto;
use Modules\Tour\Models\TourLocation; // Import Model Baru
use Modules\Tour\Models\TourPackageTime; // Import Model Baru
use Illuminate\Support\Str;

class TourDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Master Lokasi/Daerah
        $locSanur = TourLocation::create(['name' => 'Sanur', 'slug' => 'sanur']);
        $locKuta  = TourLocation::create(['name' => 'Kuta', 'slug' => 'kuta']);
        $locUbud  = TourLocation::create(['name' => 'Ubud', 'slug' => 'ubud']);

        // Master Labels
        $labelFree   = TourLabel::create(['name' => 'Free Transport', 'slug' => 'free-transport', 'icon' => 'mdi-train-car']);
        $labelLunch  = TourLabel::create(['name' => 'Lunch Included', 'slug' => 'lunch-included', 'icon' => 'mdi-island']);
        $labelIsland = TourLabel::create(['name' => 'Island Hopping', 'slug' => 'island-hopping', 'icon' => 'mdi-food']);

        // Master Tour
        $tour = Tour::create([
            'title'         => 'Eksplorasi Keajaiban Nusa Penida',
            'slug'          => Str::slug('Eksplorasi Keajaiban Nusa Penida'),
            'location'      => 'Nusa Penida, Bali',
            'overview'      => 'Nikmati perjalanan tak terlupakan mengunjungi Kelingking Beach, Broken Beach, dan Crystal Bay dalam satu paket eksklusif.',
            'opening_hours' => '07:00 - 18:00',
            'base_price'    => 750000,
            'highlights'    => [
                'Dipesan 50+ kali hari ini',
                'Golden Hour Photography',
                'Snorkeling Equipment Included',
                'Free Pick-up Hotel'
            ],
        ]);

        // Photos
        TourPhoto::create(['tour_id' => $tour->id, 'image_path' => 'tours/nusa-penida-1.jpg', 'is_primary' => true]);
        TourPhoto::create(['tour_id' => $tour->id, 'image_path' => 'tours/nusa-penida-2.jpg', 'is_primary' => false]);

        $packages = [
            [
                'package_name'     => 'Standard Sharing Tour',
                'price_per_person' => 750000,
                'details' => [
                    ['label' => 'Syarat & Ketentuan', 'content' => 'Minimal booking 2 orang. Membawa pakaian ganti.'],
                    ['label' => 'Itinerary', 'content' => '08:00 Dermaga Sanur, 09:30 Kelingking Beach, 12:00 Lunch.'],
                ],
                'times' => [
                    ['location_id' => $locSanur->id, 'time' => '07:30', 'point' => 'Dermaga Sanur Pelabuhan Baru'],
                    ['location_id' => $locKuta->id, 'time' => '06:00', 'point' => 'Lobby Hotel (Area Kuta)']
                ]
            ],
            [
                'package_name'     => 'Private VIP Tour',
                'price_per_person' => 1250000,
                'details' => [
                    ['label' => 'Syarat & Ketentuan', 'content' => 'Tidak ada batasan minimal orang.'],
                ],
                'times' => [
                    ['location_id' => $locSanur->id, 'time' => '08:30', 'point' => 'Dermaga Sanur'],
                    ['location_id' => $locUbud->id, 'time' => '07:00', 'point' => 'Coco Supermarket Ubud']
                ]
            ]
        ];

        foreach ($packages as $p) {
            $newPackage = TourPackage::create([
                'tour_id'          => $tour->id,
                'package_name'     => $p['package_name'],
                'price_per_person' => $p['price_per_person'],
            ]);

            $newPackage->labels()->attach([$labelFree->id, $labelLunch->id, $labelIsland->id]);

            // Seed Detail Package
            foreach ($p['details'] as $index => $detail) {
                TourDetail::create([
                    'tour_package_id' => $newPackage->id,
                    'label'           => $detail['label'],
                    'content'         => $detail['content'],
                    'order'           => $index + 1,
                ]);
            }

            // 2. Seed TourPackageTime (Repeater Data)
            foreach ($p['times'] as $time) {
                TourPackageTime::create([
                    'tour_package_id'  => $newPackage->id,
                    'tour_location_id' => $time['location_id'],
                    'departure_time'   => $time['time'],
                    'meeting_point'    => $time['point']
                ]);
            }

            // Availability
            TourAvailability::create([
                'tour_package_id' => $newPackage->id,
                'available_date'  => now()->addDay()->format('Y-m-d'),
                'stock'           => 10,
                'is_available'    => true,
            ]);
        }
    }
}
