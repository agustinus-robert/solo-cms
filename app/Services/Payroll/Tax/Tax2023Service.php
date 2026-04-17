<?php

namespace App\Services\Payroll\Tax;

class Tax2023Service implements PayrollTaxInterface {
    public function calculate(float $bruto, array $config, $brackets): float {
        $jabatan = min($bruto * 0.05, 500000);
        $nettoSetahun = ($bruto - $jabatan) * 12;

        $ptkp = $config['ptkp_value'] ?? 54000000;
        $pkp = max(0, floor(($nettoSetahun - $ptkp) / 1000) * 1000);

        $tax = 0; $prevMax = 0;
        foreach ($brackets as $b) {
            $currentLayer = $b->max ? ($b->max - $prevMax) : $pkp;
            if ($pkp > $currentLayer && $b->max) {
                $tax += $currentLayer * ($b->rate / 100);
                $pkp -= $currentLayer;
                $prevMax = $b->max;
            } else {
                $tax += $pkp * ($b->rate / 100);
                break;
            }
        }
        return $tax / 12;
    }
}
