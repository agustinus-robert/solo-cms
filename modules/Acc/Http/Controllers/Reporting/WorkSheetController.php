<?php

namespace Modules\Acc\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Coa;
use Modules\Acc\Models\Period;
use Illuminate\Support\Facades\DB;

// neraca lajur
class WorksheetController extends Controller
{
    public function index(Request $request)
    {
        // Ambil periode aktif atau berdasarkan request
        $period = Period::where('is_closed', false)->first();
        $coas = Coa::orderBy('code')->get();

        $worksheet = $coas->map(function ($coa) use ($period) {
            // 1. SALDO AWAL
            $initialBalance = $coa->beginningBalances()
                ->where('period_id', $period->id)
                ->first()?->amount ?? 0;

            // 2. NERACA SALDO (Hanya dari Ledger tipe 'general')
            $ns = DB::table('acc_ledger_entries')
                ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                ->where('coa_id', $coa->id)
                ->where('acc_ledgers.type', \Modules\Acc\Enums\LedgerType::GENERAL)
                ->selectRaw('SUM(debit) as d, SUM(credit) as k')
                ->first();

            // 3. PENYESUAIAN (Hanya dari Ledger tipe 'adjustment')
            $adj = DB::table('acc_ledger_entries')
                ->join('acc_ledgers', 'acc_ledgers.id', '=', 'acc_ledger_entries.ledger_id')
                ->where('coa_id', $coa->id)
                ->where('acc_ledgers.type', \Modules\Acc\Enums\LedgerType::ADJUSTMENT)
                ->selectRaw('SUM(debit) as d, SUM(credit) as k')
                ->first();

            // LOGIKA SALDO NORMAL
            $isDebitNormal = $coa->normal_balance->value === 'debit';

            // Hitung Neraca Saldo Disesuaikan (NSD)
            $totalD = ($isDebitNormal ? $initialBalance : 0) + ($ns->d ?? 0) + ($adj->d ?? 0);
            $totalK = (!$isDebitNormal ? $initialBalance : 0) + ($ns->k ?? 0) + ($adj->k ?? 0);

            $nsd_d = $totalD > $totalK ? $totalD - $totalK : 0;
            $nsd_k = $totalK > $totalD ? $totalK - $totalD : 0;

            // Tentukan apakah akun Riil (Neraca) atau Nominal (Laba Rugi)
            $isNominal = in_array($coa->category->value, ['revenue', 'expense']);

            return (object) [
                'code' => $coa->code,
                'name' => $coa->name,
                'ns'   => ['d' => ($ns->d ?? 0), 'k' => ($ns->k ?? 0)],
                'adj'  => ['d' => ($adj->d ?? 0), 'k' => ($adj->k ?? 0)],
                'nsd'  => ['d' => $nsd_d, 'k' => $nsd_k],
                'lr'   => ['d' => $isNominal ? $nsd_d : 0, 'k' => $isNominal ? $nsd_k : 0],
                'nr'   => ['d' => !$isNominal ? $nsd_d : 0, 'k' => !$isNominal ? $nsd_k : 0],
            ];
        });

        return view('acc::work-sheet.index', compact('worksheet', 'period'));
    }
}
