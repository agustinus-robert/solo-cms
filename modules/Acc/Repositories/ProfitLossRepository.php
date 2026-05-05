<?php

namespace Modules\Acc\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Acc\Models\Coa;

trait ProfitLossRepository
{
    public function getProfitLoss($periodId)
    {
        $period = \Modules\Acc\Models\Period::findOrFail($periodId);

        return Coa::query()
            ->whereIn('category', ['revenue', 'expense'])
            ->select('id', 'code', 'name', 'category')
            ->addSelect([
                'total_debit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    // Filter pakai tanggal periode, bukan period_id
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('SUM(debit)'),
                'total_credit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('SUM(credit)')
            ])
            ->get();
    }
}
