<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourLabel;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Repositories\PackageRepositories;
use Modules\Tour\Http\Requests\Package\StoreRequest;
use Modules\Tour\Http\Requests\Package\UpdateRequest;

class PackageController extends Controller
{
    use PackageRepositories;

    public function index(Request $request)
    {
        $packages = $this->getPackageTable($request);
        $tours = Tour::all();

        if ($request->ajax()) {
            return view('tour::package._table', compact('packages'))->render();
        }

        return view('tour::package.index', compact('packages', 'tours'));
    }

    public function create()
    {
        $package = null;
        $tours = Tour::all();
        $labels = TourLabel::all();
        return view('tour::package.upsert', compact('package', 'tours', 'labels'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertPackage($request->transform(), $request->label_ids ?? []);
            return redirect()->route('tour::package.index')->with('success', 'Paket berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(TourPackage $package)
    {
        $tours = Tour::all();
        $labels = TourLabel::all();
        // Load relasi ID label yang sudah terpilih
        $package->load('labels');
        return view('tour::package.upsert', compact('package', 'tours', 'labels'));
    }

    public function update(UpdateRequest $request, TourPackage $package)
    {
        try {
            $this->upsertPackage($request->transform(), $request->label_ids ?? [], $package);
            return redirect()->route('tour::package.index')->with('success', 'Paket diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(TourPackage $package)
    {
        $package->delete();
        return back()->with('success', 'Data paket dihapus');
    }
}
