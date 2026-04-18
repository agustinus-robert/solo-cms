<?php

namespace Modules\Hotel\Repositories\Guests;

use Modules\Hotel\Models\Guests;
use Illuminate\Pagination\LengthAwarePaginator;

trait GuestRepositories
{
    public function getGuests(array $filters = [], int $perPage = 10)
    {
        return Guests::query()
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                return $q->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('id_card_number', 'like', "%{$search}%")
                         ->orWhere('phone_number', 'like', "%{$search}%");
            })
            ->orderBy('first_name', 'asc')
            ->paginate($perPage);
    }

   public function upsertGuest(array $data, ?int $id = null): Guests
    {
        return Guests::updateOrCreate(
            ['id' => $id],
            [
                'id_card_number' => $data['id_card_number'],
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'] ?? null,
                'email'          => $data['email'] ?? null,
                'phone_number'   => $data['phone_number'],
            ]
        );
    }

    /**
     * Hapus data tamu.
     * Menambahkan pengecekan agar tidak menghapus tamu yang punya riwayat booking aktif.
     */
    public function deleteGuest(int $id): bool
    {
        $guest = Guests::withCount('bookings')->findOrFail($id);
        if ($guest->bookings_count > 0) {
            throw new \Exception("Data tamu tidak bisa dihapus karena memiliki riwayat reservasi.");
        }

        return $guest->delete();
    }
}
