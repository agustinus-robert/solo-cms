<?php

namespace App\Services\Payroll\Tax;

class TaxTERService implements PayrollTaxInterface
{
    public function calculate(float $bruto, array $config, $brackets): float
    {
        $category = $config['ptkp_category'];
        $rate = 0;

        foreach ($brackets as $bracket) {
            if ($bracket->class === $category) {
                if ($bruto >= $bracket->min && ($bracket->max === null || $bruto <= $bracket->max)) {
                    $rate = $bracket->rate;
                    break;
                }
            }
        }

        return $bruto * ($rate / 100);
    }
}
