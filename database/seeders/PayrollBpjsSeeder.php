<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollBpjsRule;

class PayrollBpjsSeeder extends Seeder
{
    public function run(): void
    {
        $kes = PayrollBpjsRule::create([
            'code' => 'BPJS_KES',
            'name' => 'BPJS Kesehatan',
            'formula' => 'gaji * rate_lookup',
            'effective_start' => '2024-01-01',
            'is_active' => true,
        ]);

        $kes->brackets()->createMany([
            ['class' => 1, 'min' => 0, 'max' => null, 'rate' => 0.05],
            ['class' => 2, 'min' => 0, 'max' => null, 'rate' => 0.04],
            ['class' => 3, 'min' => 0, 'max' => null, 'rate' => 0.03],
        ]);
    }
}
