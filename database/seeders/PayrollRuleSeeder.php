<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollRule;

class PayrollRuleSeeder extends Seeder
{
    public function run(): void
    {
        $ter = PayrollRule::create([
            'code' => 'TER',
            'name' => 'TER Bulanan',
            'formula' => 'gaji * rate_lookup',
            'effective_start' => '2024-01-01',
            'is_active' => true
        ]);

        $ter->brackets()->createMany([
            ['min' => 0, 'max' => 5000000, 'rate' => 0.00],
            ['min' => 5000001, 'max' => 10000000, 'rate' => 0.05],
            ['min' => 10000001, 'max' => 20000000, 'rate' => 0.10],
            ['min' => 20000001, 'max' => null, 'rate' => 0.15],
        ]);

        $pph = PayrollRule::create([
            'code' => 'PPH21',
            'name' => 'PPh 21 Pasal 17',
            'formula' => '
                bruto = penghasilan;
                pkp = max(0, bruto - ptkp);
                progressive(pkp)
            ',
            'effective_start' => '2024-01-01',
            'is_active' => true
        ]);

        $pph->brackets()->createMany([
            ['min' => 0, 'max' => 60000000, 'rate' => 0.05],
            ['min' => 60000000, 'max' => 250000000, 'rate' => 0.15],
            ['min' => 250000000, 'max' => 500000000, 'rate' => 0.25],
            ['min' => 500000000, 'max' => 5000000000, 'rate' => 0.30],
            ['min' => 5000000000, 'max' => null, 'rate' => 0.35],
        ]);

        // $calc = app(\App\Services\PayrollCalculator::class);

        // $result = $calc->calculate('PPH21', [
        //     'penghasilan' => 120000000,
        //     'gaji' => 120000000,
        //     'mariage' => 1,
        //     'child' => 2,
        //     'sex' => 3,
        //     'date' => now(),
        // ], now());
    }
}
