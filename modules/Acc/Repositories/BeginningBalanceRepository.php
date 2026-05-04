<?php

namespace Modules\Acc\Repositories;

use Modules\Acc\Models\BeginningBalance;

trait BeginningBalanceRepository
{
    public function getByPeriod($periodId)
    {
        return BeginningBalance::where('period_id', $periodId)
            ->with('coa')
            ->get();
    }

    public function upsert(array $data, $id = null)
    {
        return BeginningBalance::updateOrCreate(
            [
                'period_id' => $data['period_id'],
                'coa_id'    => $data['coa_id'],
            ],
            [
                'amount'    => $data['amount']
            ]
        );
    }
}
