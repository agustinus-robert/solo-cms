<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\Guests;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\BookingSource;
use Modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Enums\RoomTypeCategoryEnum;
use Faker\Factory as Faker;

class HotelDatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');
        $amenitiesData = [
            ['name' => 'Free WiFi', 'icon' => 'mdi-wifi'],
            ['name' => 'AC', 'icon' => 'mdi-air-conditioner'],
            ['name' => 'Breakfast', 'icon' => 'mdi-coffee'],
            ['name' => 'Mini Bar', 'icon' => 'mdi-glass-cocktail'],
            ['name' => 'Smart TV', 'icon' => 'mdi-television-classic'],
        ];

        foreach ($amenitiesData as $amn) {
            Amenity::updateOrCreate(['name' => $amn['name']], $amn);
        }

        $amnId = Amenity::pluck('id', 'name');
        $sources = ['Traveloka', 'Tiket.com', 'Agoda', 'Booking.com', 'Walk-in (Direct)'];
        foreach ($sources as $source) {
            BookingSource::updateOrCreate(
                ['name' => $source],
                ['commission_rate' => ($source == 'Walk-in (Direct)') ? 0 : 15.00]
            );
        }

        $roomTypes = [
            [
                'name' => 'Standard Single',
                'category' => RoomTypeCategoryEnum::STANDARD,
                'base_price' => 350000,
                'description' => 'Kamar nyaman untuk pelancong tunggal.',
                'amenities' => [$amnId['Free WiFi'], $amnId['AC'], $amnId['Smart TV']]
            ],
            [
                'name' => 'Deluxe Double',
                'category' => RoomTypeCategoryEnum::DELUXE,
                'base_price' => 650000,
                'description' => 'Kamar luas dengan pemandangan kota.',
                'amenities' => [$amnId['Free WiFi'], $amnId['AC'], $amnId['Breakfast'], $amnId['Smart TV']]
            ],
            [
                'name' => 'Executive Suite',
                'category' => RoomTypeCategoryEnum::SUITE,
                'base_price' => 1250000,
                'description' => 'Mewah dengan ruang tamu terpisah.',
                'amenities' => [$amnId['Free WiFi'], $amnId['AC'], $amnId['Breakfast'], $amnId['Mini Bar'], $amnId['Smart TV']]
            ],
        ];

        foreach ($roomTypes as $typeData) {
            $selectedAmenities = $typeData['amenities'];
            unset($typeData['amenities']);

            $type = RoomType::updateOrCreate(['name' => $typeData['name']], $typeData);
            $type->amenities()->sync($selectedAmenities);
        }

        // 4. Seed Kamar (Rooms)
        $types = RoomType::all();
        foreach ($types as $index => $type) {
            for ($i = 1; $i <= 5; $i++) {
                Room::updateOrCreate(
                    ['room_number' => ($index + 1) . '0' . $i],
                    [
                        'room_type_id' => $type->id,
                        'floor' => ($index + 1),
                        'status' => RoomStatusEnum::AVAILABLE
                    ]
                );
            }
        }

        for ($i = 0; $i < 10; $i++) {
            Guests::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'id_card_number' => $faker->nik,
            ]);
        }

        $this->command->info('Data Master Hotel berhasil di-seed!');
    }
}
