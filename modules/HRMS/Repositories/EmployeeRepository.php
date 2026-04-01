<?php

namespace Modules\HRMS\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Account\Repositories\UserRepository;
use Modules\Account\Repositories\User\PhoneRepository;
use Modules\HRMS\Models\Employee;

trait EmployeeRepository
{
    use UserRepository, PhoneRepository, EmployeeContractRepository;

    /**
     * Store newly created resource.
     */
    public function storeEmployee(array $data)
    {
        $user = $this->storeUser(Arr::only($data, ['name', 'username', 'password']));
        $this->updatePhone($user, Arr::only($data, ['phone_code', 'phone_number', 'phone_whatsapp']));

        $employee = new Employee(Arr::only($data, ['joined_at']));

        if ($user->employee()->save($employee)) {

            if (!isset($data['contract'])) {
                $this->storeEmployeeContract($employee, Arr::only($data, ['contract_id', 'kd', 'start_at', 'end_at', 'contract_file', 'work_location']));
            }

            return $employee;
        }
        return false;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateEmployee(Employee $employee, array $data)
    {
        if ($employee->fill(Arr::only($data, ['joined_at', 'permanent_at', 'kd', 'permanent_kd', 'permanent_sk']))->save()) {
            return $employee;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyEmployee(Employee $employee)
    {
        if (!$employee->trashed() && $employee->delete()) {
            return $employee;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreEmployee(Employee $employee)
    {
        if ($employee->trashed() && $employee->restore()) {
            return $employee;
        }
        return false;
    }
}
