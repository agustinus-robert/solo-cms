<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TaxRateResolver
{
    public function getRate(string $category, float $amount): float
    {
        $row = DB::table('ref_ter_rates')
            ->where('category', $category)
            ->where(function ($q) use ($amount) {
                $q->whereNull('lower_bound')->orWhere('lower_bound', '<=', $amount);
            })
            ->where(function ($q) use ($amount) {
                $q->whereNull('upper_bound')->orWhere('upper_bound', '>=', $amount);
            })
            ->first();

        return $row->rate ?? 0;
    }
}
