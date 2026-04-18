<?php

namespace Modules\Hotel\Repositories\Room;
use Modules\Hotel\Models\Room;

trait RoomRepositories
{
    /**
     * Simpan atau Update data kamar (Upsert Logic).
     */
    public function upsert(array $data, ?int $id = null): Room
    {
        return Room::updateOrCreate(
            ['id' => $id],
            [
                'room_number'  => $data['room_number'],
                'room_type_id' => $data['room_type_id'],
                'floor'        => $data['floor'],
                'status'       => $data['status'],
            ]
        );
    }

    /**
     * Cari kamar berdasarkan ID.
     */
    public function find(int $id): ?Room
    {
        return Room::findOrFail($id);
    }

    /**
     * Hapus kamar.
     */
    public function delete(int $id): bool
    {
        $room = $this->find($id);
        return $room->delete();
    }

    /**
     * Ambil statistik ringkas untuk Dashboard.
     */
    public function getStatusCounts(): array
    {
        return Room::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}
