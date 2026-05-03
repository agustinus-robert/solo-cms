<?php

namespace Modules\Tour\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tour\Models\Tour;
use Modules\Tour\Repositories\BookingRepositories;
use Modules\Tour\Http\Requests\Booking\StoreRequest;
use Modules\Tour\Http\Requests\Booking\UpdateRequest;

class BookingController extends Controller
{
    use BookingRepositories;

    public function index(Request $request)
    {
        $tours = $this->getTourTable($request);

        if ($request->ajax()) {
            return view('tour::booking._table', compact('tours'))->render();
        }

        return view('tour::booking.index', compact('tours'));
    }

    public function create()
    {
        $tour = null;
        return view('tour::booking.upsert', compact('tour'));
    }

    public function edit(Tour $booking)
    {
        $tour = $booking;
        return view('tour::booking.upsert', compact('tour'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertTour($request->transform());
            return redirect()->route('tour::booking.index')->with('success', 'Tour Berhasil Dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Tour $booking, UpdateRequest $request)
    {
        try {
            $this->upsertTour($request->transform(), $booking);
            return redirect()->route('tour::booking.index')->with('success', 'Tour Berhasil Diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Tour $booking)
    {
        $this->deleteTour($booking);
        return back()->with('success', 'Data dihapus');
    }
}
