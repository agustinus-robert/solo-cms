<?php

namespace Modules\HRMS\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Modules\HRMS\Models\EmployeeContract;
use Modules\HRMS\Models\EmployeePosition;

trait EmployeePositionRepository
{
    /**
     * Store newly created resource.
     */
    public function storeEmployeePosition(EmployeeContract $contract, array $data)
    {
        $position = new EmployeePosition(array_merge(
            Arr::only($data, ['position_id', 'start_at', 'end_at']),
            [
                'empl_id' => $contract->empl_id
            ]
        ));

        if ($contract->positions()->save($position)) {
            return $position;
        }
        return false;
    }

    /**
     * Update newly created resource.
     */
    public function updateEmployeePosition(EmployeePosition $position, array $data)
    {
        $position = $position->fill(Arr::only($data, ['position_id', 'start_at', 'end_at']));

        if ($position->save()) {
            return $position;
        }
        return false;
    }

    /**
     * Update newly created resource.
     */
    public function removeEmployeePosition(EmployeePosition $position)
    {
        if ($position->delete()) {
            return $position;
        }
        return false;
    }
}
