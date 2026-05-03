<?php

namespace Modules\Tour\Repositories;

use Modules\Tour\Models\TourLocation;

trait LocationRepositories
{
    /**
     * Ambil data tabel lokasi dengan filter search & pagination.
     */
    public function getLocationTable($request)
    {
        return TourLocation::latest()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Handle Create atau Update Lokasi.
     * Menggunakan Model Binding untuk Update.
     */
    public function upsertLocation(array $data, $location = null)
    {
        if ($location) {
            $location->update($data);
            return $location;
        }

        return TourLocation::create($data);
    }
}
