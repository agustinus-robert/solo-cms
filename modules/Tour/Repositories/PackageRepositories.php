<?php

namespace Modules\Tour\Repositories;

use Modules\Tour\Models\TourPackage;
use Illuminate\Support\Facades\DB;

trait PackageRepositories
{
    public function upsertPackage(array $data, array $labelIds = [], TourPackage $package = null)
    {
        return DB::transaction(function () use ($data, $labelIds, $package) {
            if ($package) {
                $package->update($data);
            } else {
                $package = TourPackage::create($data);
            }

            $package->labels()->sync($labelIds);

            return $package;
        });
    }

    public function getPackageTable($request)
    {
        return TourPackage::with(['tour', 'labels'])
            ->when($request->tour_id, function($q) use ($request) {
                $q->where('tour_id', $request->tour_id);
            })
            ->when($request->search, function($q) use ($request) {
                $q->where('package_name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);
    }
}
