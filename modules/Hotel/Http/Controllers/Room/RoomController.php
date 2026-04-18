<?php

namespace Modules\Hotel\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Room;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Enums\RoomStatusEnum;
use Modules\Hotel\Http\Requests\Room\StoreRequest;
use Modules\Hotel\Http\Requests\Room\UpdateRequest;
use Modules\Hotel\Repositories\Room\RoomRepositories;

use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    use RoomRepositories;
    /**
     * Tampilkan daftar semua kamar.
     */
    public function index(Request $request)
    {
        $rooms = Room::with('type')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type_id, fn($q) => $q->where('room_type_id', $request->type_id))
            ->orderBy('room_number', 'asc')
            ->paginate(10);

        $roomTypes = RoomType::all();
        $statuses = RoomStatusEnum::cases();

        if ($request->ajax()) {
            return view('hotel::room._table', compact('rooms'))->render();
        }

        return view('hotel::room.index', compact('rooms', 'roomTypes', 'statuses'));
    }

    /**
     * Tampilkan form tambah kamar.
     */
    public function create()
    {
        $roomTypes = RoomType::all();
        $statuses = RoomStatusEnum::cases();
        $room = [];

        return view('hotel::room.upsert', compact('roomTypes', 'statuses', 'room'));
    }

    /**
     * Simpan kamar baru.
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->transform();
            $this->upsert($data);

            return redirect()
                ->route('hotel::room.index')
                ->with('success', 'Kamar ' . $data['room_number'] . ' berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form edit kamar.
     */
    public function edit(Room $room)
    {
        $roomTypes = RoomType::all();
        $statuses = RoomStatusEnum::cases();

        return view('hotel::room.upsert', compact('roomTypes', 'statuses', 'room'));
    }

    /**
     * Update data kamar.
     */
    public function update(UpdateRequest $request, Room $room)
    {
        try {
            $data = $request->transform();
            $this->upsert($data, $room->id);

            return redirect()
                ->route('hotel::room.index')
                ->with('success', 'Kamar ' . $room->room_number . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kamar.
     */
    public function destroy(Room $room)
    {
        if ($room->status === RoomStatusEnum::OCCUPIED) {
            return back()->with('error', 'Kamar sedang terisi, tidak bisa dihapus!');
        }

        $room->delete();

        return redirect()->route('hotel::room.index')
            ->with('success', 'Kamar berhasil dihapus!');
    }
}
