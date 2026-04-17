<?php

namespace App\Services\Payroll\Tax;

class Tax2016Service implements PayrollTaxInterface
{
    public function calculate(float $bruto, array $config, $brackets): float
    {
        $biayaJabatan = min($bruto * 0.05, 500000);
        $nettoSetahun = ($bruto - $biayaJabatan) * 12;
        $pkp = max(0, floor(($nettoSetahun - $config['ptkp_value']) / 1000) * 1000);

        $taxTotal = 0; $prevMax = 0;
        foreach ($brackets as $b) {
            $range = $b->max ? ($b->max - $prevMax) : $pkp;
            if ($pkp > $range && $b->max) {
                $taxTotal += $range * ($b->rate / 100);
                $pkp -= $range; $prevMax = $b->max;
            } else {
                $taxTotal += $pkp * ($b->rate / 100);
                break;
            }
        }
        return $taxTotal / 12;
    }
}
