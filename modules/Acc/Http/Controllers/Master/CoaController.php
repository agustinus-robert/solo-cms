<?php

namespace Modules\Acc\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Acc\Models\Coa;
use Modules\Acc\Http\Requests\Coa\StoreRequest;
use Modules\Acc\Http\Requests\Coa\UpdateRequest;
use Modules\Acc\Repositories\CoaRepository;

class CoaController extends Controller
{
    use CoaRepository;

    public function index(Request $request)
    {
        $coas = Coa::query()
            ->when($request->search, function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                ->orWhere('code', 'ilike', "%{$request->search}%");
            })
            ->orderBy('code', 'asc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('acc::coa._table', compact('coas'))->render();
        }

        return view('acc::coa.index', compact('coas'));
    }

    public function create()
    {
        $coa = null;
        return view('acc::coa.upsert', compact('coa'));
    }

    public function store(StoreRequest $request)
    {
        $this->upsert($request->transform());
        return redirect()->route('acc::coa.index')->with('success', 'Akun berhasil disimpan.');
    }

    public function edit(Coa $coa)
    {
        return view('acc::coa.upsert', compact('coa'));
    }

    public function update(UpdateRequest $request, Coa $coa)
    {
        $this->upsert($request->transform(), $coa->id);
        return redirect()->route('acc::coa.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        try {
            $this->delete($id);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
