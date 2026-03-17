<?php

namespace Modules\Poz\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Poz\Models\TierTransaction;
use Illuminate\Support\Str;

trait TierTransactionRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name',
        'ref_tier_id'
    ];

    /**
     * Store newly created resource.
     */
    public function storeTier(array $data)
    {
        $brand = new TierTransaction(Arr::only($data, $this->keys));
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
        $tier = TierTransaction::find($id);

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
