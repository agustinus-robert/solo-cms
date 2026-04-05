<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\CompanyPtkp;
use Modules\Account\Enums\MariageEnum;

class PtkpDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $taxPtkpClassification = [
            'A' => [
                ['mariage' => MariageEnum::SINGLE, 'child' => 0, 'value' => 54000000],
                ['mariage' => MariageEnum::SINGLE, 'child' => 1, 'value' => 58500000],
                ['mariage' => MariageEnum::MARRY,  'child' => 1, 'value' => 58500000],
            ],
            'B' => [
                ['mariage' => MariageEnum::SINGLE, 'child' => 2, 'value' => 63000000],
                ['mariage' => MariageEnum::SINGLE, 'child' => 3, 'value' => 67500000],
                ['mariage' => MariageEnum::MARRY,  'child' => 2, 'value' => 63000000],
            ],
            'C' => [
                ['mariage' => MariageEnum::MARRY, 'child' => 3, 'value' => 67500000],
            ]
        ];

        foreach ($taxPtkpClassification as $category => $items) {
            foreach ($items as $ptkp) {
                CompanyPtkp::create([
                    'category' => $category,
                    'sex' => 3,
                    'mariage' => $ptkp['mariage'],
                    'child' => min($ptkp['child'], 3),
                    'value' => $ptkp['value'],
                    'effective_start' => '2024-01-01',
                ]);
            }
        }
    }
}
