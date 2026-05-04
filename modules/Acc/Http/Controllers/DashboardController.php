<?php

namespace Modules\Acc\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Ledger;
use Modules\Acc\Models\LedgerEntry;
use Modules\Acc\Enums\AccountCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Hitung Total Kas & Bank (Asset) -> Debit - Credit
        $totalBalance = LedgerEntry::whereHas('coa', function($q) {
                $q->where('category', AccountCategory::ASSET);
            })
            ->select(DB::raw('SUM(debit - credit) as balance'))
            ->value('balance') ?? 0;

        // 2. Hitung Piutang (Filter kode akun diawali 12... atau sesuaikan)
        $totalReceivable = LedgerEntry::whereHas('coa', function($q) {
                $q->where('code', 'like', '12%');
            })
            ->select(DB::raw('SUM(debit - credit) as balance'))
            ->value('balance') ?? 0;

        // 3. Hitung Hutang (Liability) -> Credit - Debit
        $totalPayable = LedgerEntry::whereHas('coa', function($q) {
                $q->where('category', AccountCategory::LIABILITY);
            })
            ->select(DB::raw('SUM(credit - debit) as balance'))
            ->value('balance') ?? 0;

        // 4. Data Statistik Biaya untuk Chart
        $expense_stats = LedgerEntry::whereHas('coa', function($q) {
                $q->where('category', AccountCategory::EXPENSE);
            })
            ->join('acc_coas', 'acc_ledger_entries.coa_id', '=', 'acc_coas.id')
            ->select('acc_coas.name', DB::raw('SUM(debit) as total'))
            ->groupBy('acc_coas.name')
            ->pluck('total', 'name')
            ->toArray();

        // 5. Transaksi Terakhir
        $recentTransactions = Ledger::with(['ledgerEntries.coa'])
            ->latest()
            ->take(10)
            ->get();

        if ($request->ajax()) {
            return view('acc::dashboard._table', compact('recentTransactions'))->render();
        }

        return view('acc::dashboard.index', compact(
            'totalBalance',
            'totalReceivable',
            'totalPayable',
            'recentTransactions',
            'expense_stats'
        ));
    }
}
