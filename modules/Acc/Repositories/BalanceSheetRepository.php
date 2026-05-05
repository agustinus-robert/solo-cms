<?php

namespace Modules\Acc\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Acc\Models\Coa;
use Modules\Acc\Models\Period;

trait BalanceSheetRepository
{
    public function getBalanceSheet($periodId)
    {
        $period = Period::findOrFail($periodId);

        return Coa::query()
            ->whereIn('category', ['asset', 'liability', 'equity'])
            ->select('id', 'code', 'name', 'category', 'normal_balance')
            ->addSelect([
                // 1. Ambil Saldo Awal
                'beginning' => DB::table('acc_beginning_balances')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->where('period_id', $periodId)
                    ->select('amount'),

                // 2. Ambil Mutasi Debit
                'total_debit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('SUM(debit)'),

                // 3. Ambil Mutasi Kredit
                'total_credit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->whereBetween('acc_ledgers.transaction_date', [$period->start_date, $period->end_date])
                    ->selectRaw('SUM(credit)')
            ])
            ->orderBy('code')
            ->get()
            ->map(function ($item) {
                $begin = (float) $item->beginning;
                $debit = (float) $item->total_debit;
                $credit = (float) $item->total_credit;

                // Hitung Saldo Akhir berdasarkan kategori
                if ($item->category->value === 'asset') {
                    $item->total = $begin + $debit - $credit;
                } else {
                    // Liability & Equity bertambah di Kredit
                    $item->total = $begin + $credit - $debit;
                }
                return $item;
            });
    }
}
