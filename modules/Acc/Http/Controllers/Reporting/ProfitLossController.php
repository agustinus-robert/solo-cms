<?php

namespace Modules\Acc\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Period;
use Modules\Acc\Repositories\ProfitLossRepository;

class ProfitLossController extends Controller
{
    use ProfitLossRepository;

    public function index(Request $request)
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', $periods->first()?->id);

        $report = [];
        if ($selectedPeriodId) {
            $report = $this->getProfitLoss($selectedPeriodId)->groupBy('category');
        }

        return view('acc::profit-loss.index', compact('periods', 'selectedPeriodId', 'report'));
    }
}
