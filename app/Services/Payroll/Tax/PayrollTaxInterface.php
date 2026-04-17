<?php

namespace App\Services\Payroll\Tax;

interface PayrollTaxInterface {
    public function calculate(float $bruto, array $config, $brackets): float;
}
