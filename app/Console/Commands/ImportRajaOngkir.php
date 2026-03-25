<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ImportRajaOngkir extends Command
{
    protected $signature = 'rajaongkir:import';
    protected $description = 'Import data Provinsi, Kota, dan Kecamatan dari Komerce Raja Ongkir';

    public function handle()
{
    $apiKey = env('RAJAONGKIR_API_KEY');
    $baseUrl = 'https://rajaongkir.komerce.id/api/v1/destination';

    $this->info('Memulai import data... Sikat ulang, Robert!');

    // 1. PROVINSI (Lancar)
    $responseProv = Http::withHeaders(['key' => $apiKey])->get("$baseUrl/province");
    $provinces = $responseProv->json()['data'] ?? [];
    foreach ($provinces as $prov) {
        DB::table('ref_provinces')->updateOrInsert(['id' => $prov['id']], ['name' => $prov['name']]);
    }

    // 2. KOTA (Lancar)
    foreach ($provinces as $prov) {
        $responseCity = Http::withHeaders(['key' => $apiKey])->get("$baseUrl/city/{$prov['id']}");
        $cities = $responseCity->json()['data'] ?? [];
        foreach ($cities as $city) {
            DB::table('ref_cities')->updateOrInsert(
                ['id' => $city['id']],
                ['province_id' => $prov['id'], 'name' => $city['name'], 'type' => $city['type'] ?? '']
            );
        }
    }

    // 3. KECAMATAN (DISTRICT) - INI YANG KITA PERKETAT
    $this->info('Mengambil data Kecamatan (District)...');
    $dbCities = DB::table('ref_cities')->get();
    $bar = $this->output->createProgressBar(count($dbCities));
    $bar->start();

    foreach ($dbCities as $city) {
        // Tambahkan retry supaya kalau koneksi MacBook-mu kedip, dia coba lagi
        $responseDist = Http::retry(3, 100)
            ->withHeaders(['key' => $apiKey])
            ->get("$baseUrl/district/{$city->id}");

        $resJson = $responseDist->json();
        $districts = $resJson['data'] ?? [];

        if (!empty($districts)) {
            foreach ($districts as $dist) {
                // PAKAI DB::transaction supaya kalau satu gagal, tidak ngerusak yang lain
                try {
                    DB::table('ref_districts')->updateOrInsert(
                        ['id' => $dist['id']],
                        [
                            'city_id' => $city->id,
                            'name'    => $dist['name']
                        ]
                    );
                } catch (\Exception $e) {
                    // Log error ke terminal kalau ada yang gagal insert
                    $this->error("\nGagal Insert ID {$dist['id']}: " . $e->getMessage());
                }
            }
        } else {
            // Log kalau API balikannya kosong
            // $this->warn("\nKota {$city->name} (ID: {$city->id}) tidak punya data kecamatan.");
        }
        $bar->advance();
    }

    $bar->finish();
    $this->info("\nImport selesai! Robert, coba jalankan: SELECT COUNT(*) FROM ref_districts;");
}
}
