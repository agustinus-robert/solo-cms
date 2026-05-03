<?php

namespace Modules\Tour\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourLocation;
use Modules\Tour\Repositories\LocationRepositories;
use Modules\Tour\Http\Requests\Location\StoreRequest;
use Modules\Tour\Http\Requests\Location\UpdateRequest;

class LocationController extends Controller
{
    use LocationRepositories;

    public function index(Request $request)
    {
        $locations = $this->getLocationTable($request);

        if ($request->ajax()) {
            return view('tour::location._table', compact('locations'))->render();
        }

        return view('tour::location.index', compact('locations'));
    }

    public function create()
    {
        $location = null;
        return view('tour::location.upsert', compact('location'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertLocation($request->transform());
            return redirect()->route('tour::location.index')->with('success', 'Lokasi berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(TourLocation $location)
    {
        return view('tour::location.upsert', compact('location'));
    }

    public function update(UpdateRequest $request, TourLocation $location)
    {
        try {
            $this->upsertLocation($request->transform(), $location);
            return redirect()->route('tour::location.index')->with('success', 'Lokasi berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(TourLocation $location)
    {
        $location->delete();
        return back()->with('success', 'Data dihapus (Soft Delete)');
    }
}
