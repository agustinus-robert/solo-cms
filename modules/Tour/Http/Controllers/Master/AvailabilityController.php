<?php

namespace Modules\Tour\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourAvailability;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Repositories\AvailabilityRepositories;
use Modules\Tour\Http\Requests\Availability\StoreRequest;
use Modules\Tour\Http\Requests\Availability\UpdateRequest;

class AvailabilityController extends Controller
{
    use AvailabilityRepositories;

    public function index(Request $request)
    {
        $availabilities = $this->getAvailabilityTable($request);
        $packages = TourPackage::with('tour')->get();

        if ($request->ajax()) {
            return view('tour::availability._table', compact('availabilities'))->render();
        }

        return view('tour::availability.index', compact('availabilities', 'packages'));
    }

    public function create()
    {
        $availability = null;
        $packages = TourPackage::with('tour')->get();
        return view('tour::availability.upsert', compact('availability', 'packages'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertAvailability($request->transform());
            return redirect()->route('tour::availability.index')->with('success', 'Stok tanggal berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(TourAvailability $availability)
    {
        $packages = TourPackage::with('tour')->get();
        return view('tour::availability.upsert', compact('availability', 'packages'));
    }

    public function update(UpdateRequest $request, TourAvailability $availability)
    {
        try {
            $this->upsertAvailability($request->transform(), $availability);
            return redirect()->route('tour::availability.index')->with('success', 'Stok berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(TourAvailability $availability)
    {
        $availability->delete();
        return back()->with('success', 'Data dihapus');
    }
}
