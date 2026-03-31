<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Illuminate\Http\Request;
use Modules\Core\Models\CompanySalarySlipComponent;

trait CompanySalarySlipComponentRepository
{
    public function getCompanySalarySlipComponent($request)
    {
        return $salaries = CompanySalarySlipComponent::whenTrashed($request->get('trash'))
            ->with('category', 'slip')
            ->search($request->get('search'))
            ->orderBy('id')
            ->paginate($request->get('limit', 10));
    }
    /**
     * Store newly created resource.
     */
    public function storeCompanySalarySlipComponent(array $data)
    {
        $component = new CompanySalarySlipComponent(
            Arr::only($data, ['kd', 'slip_id', 'ctg_id', 'name', 'unit', 'operate']),
        );

        if ($component->save()) {
            return $component;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanySalarySlipComponent(CompanySalarySlipComponent $component, array $data)
    {
        $component = $component->fill(
            Arr::only($data, ['kd', 'slip_id', 'ctg_id', 'name', 'unit', 'operate']),
        );

        if ($component->save()) {
            return $component;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanySalarySlipComponent(CompanySalarySlipComponent $component)
    {
        if (!$component->trashed() && $component->delete()) {
            return $component;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanySalarySlipComponent(CompanySalarySlipComponent $component)
    {
        if ($component->trashed() && $component->restore()) {
            return $component;
        }
        return false;
    }
}
