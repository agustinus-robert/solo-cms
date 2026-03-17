<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\Tier;
use Illuminate\Support\Str;

trait TierRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name',
        'type'
    ];

    /**
     * Store newly created resource.
     */
    public function storeTier(array $data)
    {
        $location = 'file_brand/' . uniqid();

        $brand = new Tier(Arr::only($data, $this->keys));
        if ($brand->save()) {
            $outletId = $data['outlet'];

            if ($outletId) {
                $brand->outlets()->attach($outletId);
            }

            return true;
        }

        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateTier(array $data, $id)
    {
        $tier = Tier::find($id);

        if ($tier->update(Arr::only($data, $this->keys))) {
            $outletId = $data['outlet'];

            if ($outletId) {
                $tier->outlets()->syncWithoutDetaching($outletId);
            }

            return true;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyInquiry($id)
    {
        if (Brand::where('id', $id)->delete()) {
            return true;
        }

        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreInquiry($id)
    {
        if (Brand::onlyTrashed()->find($id)->restore()) {
            return true;
        }
        return false;
    }
}
