<?php

namespace Modules\Acc\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Acc\Models\Coa;
use Modules\Acc\Models\Period;

trait ProfitLossRepository
{
    public function getProfitLoss($periodId)
    {
        // 1. Ambil info periode dulu untuk dapat tanggalnya
        $period = Period::findOrFail($periodId);

        return Coa::query()
            ->whereIn('category', ['revenue', 'expense'])
            ->select('id', 'code', 'name', 'category', 'normal_balance')
            ->addSelect([
                'total_debit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('COALESCE(SUM(debit), 0)'),
                'total_credit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('COALESCE(SUM(credit), 0)')
            ])
            ->orderBy('code')
            ->get()
            ->map(function ($item) {
                $debit = (float) $item->total_debit;
                $credit = (float) $item->total_credit;

                // Gunakan ->value jika category adalah Enum
                $category = $item->category instanceof \UnitEnum ? $item->category->value : $item->category;

                if ($category === 'revenue') {
                    // Sekarang ini pasti jalan untuk pendapatan
                    $item->total = $credit - $debit;
                } else {
                    // Ini untuk beban
                    $item->total = $debit - $credit;
                }

                return $item;
            });
    }
}
