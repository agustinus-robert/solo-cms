<?php

namespace Modules\Tour\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\TourReview;
use Modules\Tour\Models\Tour;

class TourReviewController extends Controller
{
    public function index(Request $request)
    {
        $tours = Tour::orderBy('title', 'asc')->get();

        $query = TourReview::with(['tour', 'user'])
            ->when($request->tour_id, function ($q) use ($request) {
                $q->where('tour_id', $request->tour_id);
            })
            ->when($request->rating, function ($q) use ($request) {
                $q->where('rating', $request->rating);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('comment', 'like', '%' . $request->search . '%');
            });

        $reviews = $query->latest()->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('tour::review._table', compact('reviews'));
        }

        return view('tour::review.index', compact('reviews', 'tours'));
    }

    public function destroy($id)
    {
        TourReview::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Review berhasil dihapus.');
    }
}
