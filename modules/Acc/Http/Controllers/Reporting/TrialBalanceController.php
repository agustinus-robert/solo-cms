<?php

namespace Modules\Acc\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Period;
use Modules\Acc\Repositories\TrialBalanceRepository;

class TrialBalanceController extends Controller
{
    use TrialBalanceRepository;

    public function index(Request $request)
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', $periods->first()?->id);

        $data = [];
        if ($selectedPeriodId) {
            $data = $this->getTrialBalance($selectedPeriodId);
        }

        return view('acc::trial-balance.index', compact('periods', 'selectedPeriodId', 'data'));
    }
}
