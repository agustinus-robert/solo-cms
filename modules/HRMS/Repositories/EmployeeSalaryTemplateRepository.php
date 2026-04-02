<?php

namespace Modules\HRMS\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Modules\HRMS\Models\Employee;
use Modules\HRMS\Models\EmployeeSalary;
use Modules\HRMS\Models\EmployeeSalaryTemplate;

trait EmployeeSalaryTemplateRepository
{
    /**
     * Store newly created resource.
     */
    public function storeEmployeeSalaryTemplate(Employee $employee, array $data)
    {
        if ($salary = $employee->salaryTemplate()->create(Arr::only($data, ['name', 'prefix', 'cmp_template_id', 'start_at', 'end_at']))) {
            $salary->items()->createMany($data['items']);
            return $salary;
        }
        return false;
    }

    /**
     * Update resource.
     */
    public function updateEmployeeSalaryTemplate(EmployeeSalaryTemplate $template, array $data)
    {
        $template->fill(Arr::only($data, ['name', 'prefix', 'start_at', 'end_at']));

        foreach ($data['items'] as $item) {
            $items[$item['component_id']] = $item;
        }

        if (count(array_filter($items)) && $template->save()) {
            $template->components()->sync($items);
            return $template;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyEmployeeSalaryTemplate(EmployeeSalaryTemplate $template)
    {
        if (!$template->trashed() && $template->delete()) {
            return $template;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreEmployeeSalaryTemplate(EmployeeSalaryTemplate $template)
    {
        if ($template->trashed() && $template->restore()) {
            return $template;
        }
        return false;
    }
}
