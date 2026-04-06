<?php

namespace App\Services;

use Modules\Account\Enums\MariageEnum;
use Modules\Account\Enums\SexEnum;
use Illuminate\Support\Facades\DB;

class TerCategoryResolver
{
    public function resolve($marriageStatus, $childrenCount, $sex): array
    {
        $childrenCount = intval($childrenCount);
        $ter = 'A';
        $status = 'TK/0';

        if ($sex == SexEnum::FEMALE->value) {
            $ter = 'A';
            $status = 'TK/0';
        } else {
            if ($marriageStatus == MariageEnum::SINGLE->value) {
                $ter = ($childrenCount >= 2) ? 'B' : 'A';
                $status = 'TK/' . min($childrenCount, 3);
            } elseif ($marriageStatus == MariageEnum::MARRY->value) {
                if ($childrenCount == 0) $ter = 'A';
                elseif ($childrenCount <= 2) $ter = 'B';
                else $ter = 'C';

                $status = 'K/' . min($childrenCount, 3);
            }
        }

        return [
            'category' => $ter,
            'status'   => $status,
            'rates'    => $this->getRatesByCategory($ter),
        ];
    }

    protected function getRatesByCategory(string $category)
    {
        return DB::table('ref_ter_rates')
            ->where('category', $category)
            ->orderBy('lower_bound', 'asc')
            ->get()
            ->map(fn($item) => [
                'ctg'        => $item->category,
                'lower'      => $item->lower_bound,
                'upper'      => $item->upper_bound,
                'percentage' => $item->rate,
            ])
            ->toArray();
    }
}
