<?php

namespace Modules\Hotel\Repositories\Master;

use Modules\Hotel\Models\Amenity;
use Illuminate\Support\Facades\DB;

trait AmenityRepositories
{
    /**
     * Simpan atau Update Amenity.
     */
    public function upsertAmenity(array $data, ?int $id = null): Amenity
    {
        return Amenity::updateOrCreate(
            ['id' => $id],
            [
                'name' => $data['name'],
                'icon' => $data['icon'] ?? null,
            ]
        );
    }

    /**
     * Hapus Amenity dengan proteksi relasi (opsional).
     */
    public function deleteAmenity(int $id): bool
    {
        $amenity = Amenity::findOrFail($id);
        return $amenity->delete();
    }
}
