<?php

namespace App\Services\Payroll\Tax;

use Carbon\Carbon;
use App\Services\Payroll\TerRateService;

class Tax2024Service implements PayrollTaxInterface
{
    protected $terRateService;

    public function __construct()
    {
        $this->terRateService = new TerRateService();
    }

    public function calculate(float $bruto, array $config, $brackets): float
    {
        if (isset($config['is_daily']) && $config['is_daily'] === true) {
            $rate = ($bruto <= 450000) ? 0 : 0.5;
            return floor($bruto * ($rate / 100));
        }

        $date = Carbon::parse($config['start_at']);
        if ($date->month != 12) {
            $category = $config['ptkp_category'] ?? 'A';
            $rates = $this->terRateService->getRatesByCategory($category);

            $rate = 0;
            foreach ($rates as $r) {
                if ($bruto >= $r['lower'] && ($r['upper'] === null || $bruto < $r['upper'])) {
                    $rate = $r['percentage'];
                    break;
                }
            }
            return floor($bruto * ($rate / 100));
        }

        $brutoSetahun = $config['total_bruto_setahun'] ?? ($bruto * 12);
        $pphTerbayar  = $config['total_pph_jan_nov'] ?? 0;

        $pajakSetahun = $this->calculateAnnualProgresive($brutoSetahun, $config);

        return max(0, $pajakSetahun - $pphTerbayar);
    }

    /**
     * Helper untuk hitung total pajak progresif setahun (Pasal 17)
     */
    private function calculateAnnualProgresive($brutoSetahun, $config): float
    {
        $biayaJabatan = min($brutoSetahun * 0.05, 6000000);

        $nettoSetahun = $brutoSetahun - $biayaJabatan;
        $ptkp = $config['ptkp_value'] ?? 54000000;
        $pkp = max(0, floor(($nettoSetahun - $ptkp) / 1000) * 1000);
        $progressiveBrackets = $this->terRateService->getProgressiveBrackets();

        $taxTotal = 0;
        $prevMax = 0;

        foreach ($progressiveBrackets as $b) {
            if ($pkp <= 0) break;
            $range = $b['max'] ? ($b['max'] - $prevMax) : $pkp;

            if ($pkp > $range && $b['max']) {
                $taxTotal += $range * ($b['rate'] / 100);
                $pkp -= $range;
                $prevMax = $b['max'];
            } else {
                $taxTotal += $pkp * ($b['rate'] / 100);
                $pkp = 0;
                break;
            }
        }

        return $taxTotal;
    }
}
