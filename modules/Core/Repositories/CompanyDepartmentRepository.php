<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Modules\Core\Models\CompanyDepartment;

trait CompanyDepartmentRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'kd', 'name', 'description', 'parent_id', 'is_visible', 'grade_id'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyDepartment(array $data)
    {
        $department = new CompanyDepartment(Arr::only($data, $this->keys));
        if($department->save()) {
            return $department;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyDepartment(CompanyDepartment $department, array $data)
    {
        $department = $department->fill(Arr::only($data, $this->keys));
            if($department->save()) {
                return $department;
            }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanyDepartment(CompanyDepartment $department)
    {
        if(!$department->trashed() && $department->delete()) {
            return $department;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyDepartment(CompanyDepartment $department)
    {
        if($department->trashed() && $department->restore()) {
            return $department;
        }
        return false;
    }
}
