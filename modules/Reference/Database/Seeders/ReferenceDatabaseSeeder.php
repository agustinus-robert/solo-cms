<?php

namespace Modules\Reference\Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ReferenceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(file_get_contents(__DIR__.'/../Migrations/references.sql'));

        $this->command->info('Seeding RajaOngkir Provinces...');
        DB::unprepared(file_get_contents(__DIR__.'/sql/rajongkir_province.sql'));

        $this->command->info('Seeding RajaOngkir Cities...');
        DB::unprepared(file_get_contents(__DIR__.'/sql/rajongkir_cities.sql'));

        $this->command->info('Seeding RajaOngkir Districts...');
        DB::unprepared(file_get_contents(__DIR__.'/sql/rajongkir_districts.sql'));
    }
}
