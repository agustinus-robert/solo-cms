<?php

namespace Modules\HRMS\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeContract;

trait EmployeeContractRepository
{
    /**
     * Store newly created resource.
     */
    public function storeEmployeeContract(Employee $employee, array $data)
    {
        $contract = new EmployeeContract(Arr::only($data, ['contract_id', 'kd', 'start_at', 'end_at', 'work_location']));

        if ($employee->contracts()->save($contract)) {

            // Handle document file
            if (isset($data['contract_file'])) {
                $document = $contract->firstOrCreateDocument(
                    $title = 'Perjanjian Kerja - ' . $contract->kd . ' - ' . $contract->created_at->getTimestamp(),
                    $path = $data['contract_file']->store('employee/contracts', 'docs')
                );
            }

            return $employee;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyEmployeeContract(EmployeeContract $contract)
    {
        if (!$contract->trashed() && $contract->delete()) {
            return $contract;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreEmployeeContract(EmployeeContract $contract)
    {
        if ($contract->trashed() && $contract->restore()) {
            return $contract;
        }
        return false;
    }
}
