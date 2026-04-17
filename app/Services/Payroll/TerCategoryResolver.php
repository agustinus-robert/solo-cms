<?php

namespace App\Services\Payroll;
use Modules\Core\Models\CompanyPtkp;
use App\Models\PayrollRule;

class TerCategoryResolver {
   public function resolve($marriage, $child, $sex, $date)
    {
        $ptkp = CompanyPtkp::where('sex', $sex)
            ->where('mariage', $marriage)
            ->where('child', $child)
            ->first();

        $category = $ptkp->category ?? 'A';
        $rateService = new TerRateService();
        $rates = $rateService->getRatesByCategory($category);

        return [
            'status'   => ($marriage == 1 ? 'K/' : 'TK/') . $child,
            'category' => $category,
            'value'    => $ptkp->value ?? 54000000,
            'rates'    => $rates
        ];
    }

    private function formatPtkpStatus($m, $c) {
        $prefix = ($m == 1) ? 'K' : 'TK';
        return $prefix . '/' . $c;
    }
}
