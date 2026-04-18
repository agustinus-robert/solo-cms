<?php

namespace Modules\Hotel\Repositories\Room;

use Modules\Hotel\Models\RoomType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

trait RoomTypeRepositories
{
    /**
     * Simpan atau Update Tipe Kamar.
     */
    public function upsertType(array $data, ?int $id = null): RoomType
    {
        return DB::transaction(function () use ($data, $id) {
            $roomType = RoomType::updateOrCreate(
                ['id' => $id],
                [
                    'name'        => $data['name'],
                    'base_price'  => $data['base_price'],
                    'capacity'    => $data['capacity'],
                    'description' => $data['description'] ?? null,
                ]
            );

            if (isset($data['amenities'])) {
                $roomType->amenities()->sync($data['amenities']);
            } else {
                $roomType->amenities()->sync([]);
            }

            return $roomType;
        });
    }

    /**
     * Hapus tipe kamar dengan pengecekan relasi.
     */
    public function deleteType(int $id): bool
    {
        $type = RoomType::withCount('rooms')->findOrFail($id);

        if ($type->rooms_count > 0) {
            throw new \Exception("Gagal menghapus! Tipe ini masih memiliki unit kamar.");
        }

        return $type->delete();
    }
}
