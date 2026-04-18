<?php

namespace Modules\Hotel\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Http\Requests\Amenity\StoreRequest;
use Modules\Hotel\Http\Requests\Amenity\UpdateRequest;
use Modules\Hotel\Repositories\Master\AmenityRepositories;

class AmenityController extends Controller
{
    use AmenityRepositories;

    public function index(Request $request)
    {
        $amenities = Amenity::latest()->get();

        if ($request->ajax()) {
            return view('hotel::amenity._table', compact('amenities'))->render();
        }

        return view('hotel::amenity.index', compact('amenities'));
    }

    public function create()
    {
        $amenity = null;
        return view('hotel::amenity.upsert', compact('amenity'));
    }

    public function store(StoreRequest $request)
    {
        try {
            $this->upsertAmenity($request->transform());

            return redirect()->route('hotel::amenity.index')
                ->with('success', 'Fasilitas berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambah fasilitas: ' . $e->getMessage());
        }
    }

    public function edit(Amenity $amenity)
    {
        return view('hotel::amenity.upsert', compact('amenity'));
    }

    public function update(UpdateRequest $request, Amenity $amenity)
    {
        try {
            $this->upsertAmenity($request->transform(), $amenity->id);

            return redirect()->route('hotel::amenity.index')
                ->with('success', 'Fasilitas berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui fasilitas: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->deleteAmenity($id);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
