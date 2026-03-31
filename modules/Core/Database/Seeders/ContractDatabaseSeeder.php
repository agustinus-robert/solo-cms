<?php

namespace Modules\Core\Database\Seeders;

use Modules\Core\Models\CompanyContract;
use Illuminate\Database\Seeder;

class ContractDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contracts = [
            'pkwtt' => 'PKWTT (Karyawan Tetap)',
            'pkwt' => 'PKWT (Karyawan tidak tetap)',
            'part-time' => 'Part Time (Paruh Waktu)',
            'freelance' => 'Freelance',
            'magang' => 'Magang',
            'outsourching' => 'Outsourching'
        ];

        foreach ($contracts as $kd => $name) {
            CompanyContract::create([
                'kd'    => $kd,
                'name'  => $name,
            ]);
        }
    }
}
