<?php

namespace Modules\Hotel\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Http\Requests\RoomType\StoreRequest;
use Modules\Hotel\Http\Requests\RoomType\UpdateRequest;
use Modules\Hotel\Repositories\Room\RoomTypeRepositories;
use Modules\Hotel\Models\Amenity;

class RoomTypeController extends Controller
{
    use RoomTypeRepositories;

    /**
     * Menampilkan daftar tipe kamar & harga.
     */
    public function index(Request $request)
    {
        $roomTypes = RoomType::withCount('rooms')
            ->orderBy('name', 'asc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('hotel::room-type._table', compact('roomTypes'))->render();
        }

        return view('hotel::room-type.index', compact('roomTypes'));
    }

    public function create()
    {
        $amenities = Amenity::orderBy('name', 'asc')->get();
        return view('hotel::room-type.upsert', ['type' => null, 'amenities' => $amenities]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $data = $request->transform();
            $this->upsertType($data);

            return redirect()
                ->route('hotel::room-types.index')
                ->with('success', 'Tipe kamar ' . $data['name'] . ' berhasil ditambahkan!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tipe kamar: ' . $e->getMessage());
        }
    }

    public function edit(RoomType $roomType)
    {
        $amenities = Amenity::orderBy('name', 'asc')->get();
        $roomType->load('amenities');

        return view('hotel::room-type.upsert', ['type' => $roomType, 'amenities' => $amenities]);
    }

    public function update(UpdateRequest $request, RoomType $roomType)
    {
        try {
            $data = $request->transform();
            $this->upsertType($data, $roomType->id);

            return redirect()
                ->route('hotel::room-types.index')
                ->with('success', 'Tipe kamar ' . $roomType->name . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui tipe kamar: ' . $e->getMessage());
        }
    }

    /**
     * Hapus tipe kamar.
     */
    public function destroy(RoomType $roomType)
    {
        try {
            $this->deleteType($roomType->id);

            $message = 'Tipe kamar ' . $roomType->name . ' berhasil dihapus!';

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('hotel::room-types.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
