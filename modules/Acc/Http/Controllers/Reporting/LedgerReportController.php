<?php

namespace Modules\Acc\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Acc\Models\Coa;

class LedgerReportController extends Controller
{
    public function index(Request $request)
    {
        $coas = Coa::orderBy('code')->get();

        $coaId = $request->get('coa_id');
        $viewType = $request->get('view_type', 'stafel'); // Default stafel
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $report = null;

        if ($coaId) {
            $coa = Coa::findOrFail($coaId);

            $prevData = DB::table('acc_ledger_entries')
                ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                ->where('coa_id', $coaId)
                ->where('acc_ledgers.transaction_date', '<', $startDate)
                ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
                ->first();

            // Gunakan ?? 0 untuk menghindari error null saat data kosong
            $totalDebit = $prevData->total_debit ?? 0;
            $totalCredit = $prevData->total_credit ?? 0;

            if ($coa->normal_balance->value === 'debit') {
                $initialBalance = $totalDebit - $totalCredit;
            } else {
                $initialBalance = $totalCredit - $totalDebit;
            }

            $mutations = DB::table('acc_ledger_entries')
                ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                ->where('coa_id', $coaId)
                ->whereBetween('acc_ledgers.transaction_date', [$startDate, $endDate])
                ->orderBy('acc_ledgers.transaction_date')
                ->orderBy('acc_ledgers.id')
                ->select(
                    'acc_ledgers.transaction_date',
                    'acc_ledgers.reference_number',
                    'acc_ledgers.description',
                    'acc_ledger_entries.debit',
                    'acc_ledger_entries.credit'
                )
                ->get();

            $report = [
                'coa' => $coa,
                'initial_balance' => (float)$initialBalance,
                'mutations' => $mutations
            ];
        }

        return view('acc::report-ledger.index', compact('coas', 'report', 'startDate', 'endDate', 'coaId', 'viewType'));
    }
}
