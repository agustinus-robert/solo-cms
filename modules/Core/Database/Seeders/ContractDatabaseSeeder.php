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
            'honor' => 'SK Honorer',
            'tetap' => 'SK Tetap'
        ];

        foreach ($contracts as $kd => $name) {
            CompanyContract::create([
                'kd'    => $kd,
                'name'  => $name,
            ]);
        }
    }
}
