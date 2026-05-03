<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourPackage;
use Modules\Tour\Models\TourDetail;

class PackageDetailController extends Controller
{
    public function show(TourPackage $package)
    {
        $details = $package->details;
        return view('tour::package.detail.index', compact('package', 'details'));
    }

    public function store(Request $request, TourPackage $package)
    {
        $package->details()->create([
            'label' => $request->label,
            'content' => $request->content,
            'order' => $package->details()->count() + 1
        ]);

        return back()->with('success', 'Detail berhasil ditambahkan');
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $order) {
            TourDetail::where('id', $order['id'])->update(['order' => $order['position']]);
        }
        return response()->json(['status' => 'success']);
    }

    public function destroy(TourDetail $package)
    {
        $package->delete();
        return back()->with('success', 'Detail dihapus');
    }
}
