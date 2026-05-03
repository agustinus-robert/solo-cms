<?php

namespace Modules\Tour\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourLabel;
use Modules\Tour\Repositories\LabelRepositories;
use Modules\Tour\Http\Requests\Label\StoreRequest;
use Modules\Tour\Http\Requests\Label\UpdateRequest;

class LabelController extends Controller
{
    use LabelRepositories;

    public function index(Request $request)
    {
        $labels = $this->getLabelTable($request);

        if ($request->ajax()) {
            return view('tour::label._table', compact('labels'))->render();
        }

        return view('tour::label.index', compact('labels'));
    }

    public function create()
    {
        $label = null;
        return view('tour::label.upsert', compact('label'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertLabel($request->transform());
            return redirect()->route('tour::label.index')->with('success', 'Fasilitas berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(TourLabel $label)
    {
        return view('tour::label.upsert', compact('label'));
    }

    public function update(UpdateRequest $request, TourLabel $label)
    {
        try {
            $this->upsertLabel($request->transform(), $label);
            return redirect()->route('tour::label.index')->with('success', 'Fasilitas berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(TourLabel $label)
    {
        $label->delete();
        return back()->with('success', 'Data dihapus (Soft Delete)');
    }
}
