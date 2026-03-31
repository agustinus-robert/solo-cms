<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Illuminate\Http\Request;
use Modules\Core\Models\CompanySalarySlip;

trait CompanySalarySlipRepository
{
    public function getCompanySalarySlip($request)
    {
        return $slips = CompanySalarySlip::whenTrashed($request->get('trash'))
            ->search($request->get('search'))
            ->orderBy('az')
            ->paginate($request->get('limit', 10));
    }
    /**
     * Store newly created resource.
     */
    public function storeCompanySalarySlip(array $data)
    {
        $slip = new CompanySalarySlip(Arr::only($data, ['az', 'name']));
        if ($slip->save()) {
            return $slip;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanySalarySlip(CompanySalarySlip $slip, array $data)
    {
        $slip = $slip->fill(Arr::only($data, ['az', 'name']));
        if ($slip->save()) {
            return $slip;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanySalarySlip(CompanySalarySlip $slip)
    {
        if (!$slip->trashed() && $slip->delete()) {
            return $slip;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanySalarySlip(CompanySalarySlip $slip)
    {
        if ($slip->trashed() && $slip->restore()) {
            return $slip;
        }
        return false;
    }
}
