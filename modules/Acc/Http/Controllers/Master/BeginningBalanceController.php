<?php

namespace Modules\Acc\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Period;
use Modules\Acc\Models\Coa;
use Modules\Acc\Repositories\BeginningBalanceRepository;
use Modules\Acc\Http\Requests\BeginningBalance\StoreRequest;

class BeginningBalanceController extends Controller
{
    use BeginningBalanceRepository;

    public function index(Request $request)
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        $selectedPeriodId = $request->get('period_id', $periods->first()?->id);

        $coas = Coa::orderBy('code', 'asc')->get();

        // Ambil saldo yang sudah ada untuk periode ini
        $existingBalances = $this->getByPeriod($selectedPeriodId)->pluck('amount', 'coa_id');

        return view('acc::beginning-balance.index', compact(
            'periods',
            'selectedPeriodId',
            'coas',
            'existingBalances'
        ));
    }

    public function store(StoreRequest $request)
    {
        // Ambil data dari transform()
        $data = $request->transform();

        foreach ($data as $item) {
            $this->upsert($item);
        }

        return redirect()->back()->with('success', 'Saldo awal periode berhasil disimpan.');
    }
}
