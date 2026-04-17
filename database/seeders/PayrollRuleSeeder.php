<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollRule;

class PayrollRuleSeeder extends Seeder
{
    public function run(): void
    {
        PayrollRule::create([
            'code'            => 'PPH21', // Samakan kodenya
            'name'            => 'PPh 21 Pasal 17 (2016-2022)',
            'formula'         => 'App\Services\Payroll\Tax\Tax2016Service',
            'effective_start' => '2016-01-01',
            'effective_end'   => '2022-12-31',
        ]);

        PayrollRule::create([
            'code'            => 'PPH21', // Samakan kodenya
            'name'            => 'PPh 21 Pasal 17 (2023)',
            'formula'         => 'App\Services\Payroll\Tax\Tax2023Service',
            'effective_start' => '2023-01-01',
            'effective_end'   => '2023-12-31',
        ]);

        PayrollRule::create([
            'code'            => 'PPH21', // Pakai PPH21 saja agar query 'first()' di Calculator ketemu
            'name'            => 'PPh 21 TER & Pasal 17 (2024)',
            'formula'         => 'App\Services\Payroll\Tax\Tax2024Service',
            'effective_start' => '2024-01-01',
            'effective_end'   => null,
        ]);
    }
}
