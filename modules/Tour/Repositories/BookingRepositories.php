<?php

namespace Modules\Tour\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Tour\Models\Tour;

trait BookingRepositories
{
    /**
     * Menangani simpan dan update Master Tour
     */
    public function upsertTour(array $data, Tour $tour = null)
    {
        return DB::transaction(function () use ($data, $tour) {
            $data['slug'] = Str::slug($data['title']);

            if ($tour) {
                $tour->update($data);
            } else {
                $tour = Tour::create($data);
            }

            return $tour;
        });
    }

    public function deleteTour(Tour $tour)
    {
        return DB::transaction(function () use ($tour) {
            return $tour->delete();
        });
    }

    public function getTourTable($request)
    {
        return Tour::withCount(['packages'])
            ->where('deleted_at', null)
            ->when($request->search, function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
}
