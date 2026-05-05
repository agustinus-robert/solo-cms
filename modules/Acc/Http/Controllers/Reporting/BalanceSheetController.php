<?php

namespace Modules\Acc\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Period;
use Modules\Acc\Repositories\BalanceSheetRepository;
use Modules\Acc\Repositories\ProfitLossRepository;

class BalanceSheetController extends Controller
{
    use BalanceSheetRepository, ProfitLossRepository;

    public function index(Request $request)
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', $periods->first()?->id);

        $report = [];
        $netProfit = 0;

        if ($selectedPeriodId) {
            $report = $this->getBalanceSheet($selectedPeriodId)->groupBy('category.value');

            // Hitung Laba Rugi untuk dimasukkan ke Ekuitas
            $profitLossData = $this->getProfitLoss($selectedPeriodId);
            $rev = $profitLossData->where('category.value', 'revenue')->sum('total');
            $exp = $profitLossData->where('category.value', 'expense')->sum('total');
            $netProfit = $rev - $exp;
        }

        return view('acc::balance-sheet.index', compact('periods', 'selectedPeriodId', 'report', 'netProfit'));
    }
}
