<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\TourPackageTime;
use Modules\Tour\Models\TourLocation;

class PackageTimeController extends Controller
{
    public function show(TourPackage $package)
    {
        $package->load('times.location');
        $locations = TourLocation::orderBy('name', 'asc')->get();

        return view('tour::package.time.index', compact('package', 'locations'));
    }

    public function store(Request $request, TourPackage $package)
    {
        $request->validate([
            'tour_location_id' => 'required|exists:tour_locations,id',
            'departure_time'   => 'required',
            'meeting_point'    => 'nullable|string|max:255',
        ]);

        TourPackageTime::updateOrCreate(
            ['id' => $request->id],
            [
                'tour_package_id'  => $package->id,
                'tour_location_id' => $request->tour_location_id,
                'departure_time'   => $request->departure_time,
                'meeting_point'    => $request->meeting_point,
            ]
        );

        return redirect()->back()->with('success', 'Jadwal keberangkatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        TourPackageTime::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
