<?php

namespace Modules\Acc\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Acc\Models\Coa;

trait TrialBalanceRepository
{
    public function getTrialBalance($periodId)
    {
        return Coa::query()
            ->select('acc_coas.*')
            // Ambil Saldo Awal
            ->addSelect([
                'beginning_balance' => DB::table('acc_beginning_balances')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->where('period_id', $periodId)
                    ->select('amount')
                    ->limit(1)
            ])
            // Hitung Total Mutasi Debit
            ->addSelect([
                'total_debit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    // Logic: transaksi harus di dalam rentang periode (atau filter period_id jika ada)
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->selectRaw('SUM(debit)')
            ])
            // Hitung Total Mutasi Kredit
            ->addSelect([
                'total_credit' => DB::table('acc_ledger_entries')
                    ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                    ->whereColumn('coa_id', 'acc_coas.id')
                    ->selectRaw('SUM(credit)')
            ])
            ->orderBy('code', 'asc')
            ->get()
            ->map(function ($item) {
                $begin = $item->beginning_balance ?? 0;
                $debit = $item->total_debit ?? 0;
                $credit = $item->total_credit ?? 0;

                // Hitung Saldo Akhir berdasarkan Normal Balance
                if ($item->normal_balance == 'debit') {
                    $item->ending_balance = $begin + $debit - $credit;
                } else {
                    $item->ending_balance = $begin + $credit - $debit;
                }

                return $item;
            });
    }
}
