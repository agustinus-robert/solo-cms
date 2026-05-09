<?php

namespace Modules\Acc\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Ledger;
use Modules\Acc\Models\Coa;
use Modules\Acc\Repositories\LedgerRepository;
use Modules\Acc\Http\Requests\Ledger\StoreRequest;
use Modules\Acc\Http\Requests\Ledger\UpdateRequest;

class LedgerController extends Controller
{
    use LedgerRepository;

    public function index(Request $request)
    {
        $ledgers = $this->getAll($request->all());
        return view('acc::ledger.index', compact('ledgers'));
    }

    public function create()
    {
        $coas = Coa::orderBy('code', 'asc')->get();
        $ledger = null;
        return view('acc::ledger.upsert', compact('coas', 'ledger'));
    }

    public function store(StoreRequest $request)
    {
        $this->upsert($request->transform());
        return redirect()->route('acc::ledger.index')->with('success', 'Jurnal Berhasil Disimpan');
    }

    public function edit(Ledger $ledger)
    {
        $ledger->load('ledgerEntries.coa');

        $coas = Coa::orderBy('code', 'asc')->get();
        return view('acc::ledger.upsert', compact('ledger', 'coas'));
    }

    public function update(UpdateRequest $request, Ledger $ledger)
    {
        $this->upsert($request->transform(), $ledger->id);
        return redirect()->route('acc::ledger.index')->with('success', 'Jurnal Berhasil Diperbarui');
    }

    public function destroy(Ledger $ledger)
    {
        $this->delete($ledger->id);
        return redirect()->back()->with('success', 'Jurnal Berhasil Dihapus');
    }
}
