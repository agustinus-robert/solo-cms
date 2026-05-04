<?php

namespace Modules\Tour\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourPackageTime;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\TourLocation;

class PackageTimeReportController extends Controller
{
    public function index(Request $request)
    {
        $packages = TourPackage::orderBy('package_name', 'asc')->get();
        $locations = TourLocation::orderBy('name', 'asc')->get();

        $times = $this->fetchData($request);

        if ($request->ajax()) {
            return view('tour::reporting._table', compact('times'));
        }

        return view('tour::reporting.index', compact('times', 'packages', 'locations'));
    }

    private function fetchData($request)
    {
        return TourPackageTime::with(['package.tour', 'location'])
            ->when($request->package_id, function ($q) use ($request) {
                $q->where('tour_package_id', $request->package_id);
            })
            ->when($request->location_id, function ($q) use ($request) {
                $q->where('tour_location_id', $request->location_id);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('package', function ($inner) use ($request) {
                    $inner->where('package_name', 'like', '%' . $request->search . '%');
                })->orWhere('meeting_point', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
