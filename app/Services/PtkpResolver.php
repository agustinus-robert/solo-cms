<?php

namespace App\Services;

use Modules\Core\Models\CompanyPtkp;
use Carbon\Carbon;

class PtkpResolver
{
    public function resolve(array $context, $date)
    {
        $date = Carbon::parse($date);

        $child = min($context['child'] ?? 0, 3);

        return CompanyPtkp::query()
            ->where('mariage', $context['mariage'])
            ->where('child', $child)
            ->where(function ($q) use ($context) {
                $q->whereNull('sex')
                  ->orWhere('sex', $context['sex'] ?? 3);
            })
            ->where('effective_start', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_end')
                  ->orWhere('effective_end', '>=', $date);
            })
            ->orderByDesc('effective_start')
            ->value('value') ?? 0;
    }
}
