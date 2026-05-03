<?php

namespace Modules\Tour\Repositories;

use Modules\Tour\Models\TourAvailability;
use Illuminate\Support\Facades\DB;

trait AvailabilityRepositories
{
    /**
     * Simpan atau update stok per tanggal
     */
    public function upsertAvailability(array $data, TourAvailability $availability = null)
    {
        return DB::transaction(function () use ($data, $availability) {
            if ($availability) {
                $availability->update($data);
                return $availability;
            }

            return TourAvailability::updateOrCreate(
                [
                    'tour_package_id' => $data['tour_package_id'],
                    'available_date'  => $data['available_date'],
                ],
                [
                    'stock'        => $data['stock'],
                    'is_available' => $data['is_available'] ?? true,
                ]
            );
        });
    }

    public function getAvailabilityTable($request)
    {
        return TourAvailability::with('package.tour')
            ->when($request->package_id, function($q) use ($request) {
                $q->where('tour_package_id', $request->package_id);
            })
            ->when($request->date, function($q) use ($request) {
                $q->whereDate('available_date', $request->date);
            })
            ->latest('available_date')
            ->paginate(10);
    }
}
