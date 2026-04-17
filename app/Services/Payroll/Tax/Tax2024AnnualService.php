<?php

namespace App\Services\Payroll\Tax;

class Tax2024AnnualService implements PayrollTaxInterface
{
    public function calculate(float $bruto, array $config, $brackets): float
    {
        $brutoSetahun = $config['total_bruto_setahun'] ?? ($bruto * 12);
        $biayaJabatan = min($brutoSetahun * 0.05, 6000000);

        $iuran = $config['total_iuran_setahun'] ?? 0;
        $nettoSetahun = $brutoSetahun - $biayaJabatan - $iuran;

        $ptkp = $config['ptkp_value'] ?? 54000000;
        $pkp = max(0, floor(($nettoSetahun - $ptkp) / 1000) * 1000);
        $totalPajakSetahun = 0;
        $prevMax = 0;
        foreach ($brackets as $b) {
            $range = $b->max ? ($b->max - $prevMax) : $pkp;
            if ($pkp > $range && $b->max) {
                $totalPajakSetahun += $range * ($b->rate / 100);
                $pkp -= $range;
                $prevMax = $b->max;
            } else {
                $totalPajakSetahun += $pkp * ($b->rate / 100);
                break;
            }
        }

        $pajakSudahDibayar = $config['total_pph_jan_nov'] ?? 0;
        return max(0, $totalPajakSetahun - $pajakSudahDibayar);
    }
}
