<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Illuminate\Http\Request;
use Modules\Core\Models\CompanySalarySlipCategory;

trait CompanySalarySlipCategoryRepository
{
    public function getCompanySalaryCategories($request)
    {
        return $categories = CompanySalarySlipCategory::with('slip')->whenTrashed($request->get('trash'))
            ->whereHas('slip')
            ->search($request->get('search'))
            ->orderBy('slip_id')
            ->orderBy('az')
            ->paginate($request->get('limit', 10));
    }
    /**
     * Store newly created resource.
     */
    public function storeCompanySalarySlipCategory(array $data)
    {
        $category = new CompanySalarySlipCategory(
            Arr::only($data, ['slip_id', 'az', 'name'])
        );

        if ($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanySalarySlipCategory(CompanySalarySlipCategory $category, array $data)
    {
        $category = $category->fill(
            Arr::only($data, ['slip_id', 'az', 'name']),
        );

        if ($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanySalarySlipCategory(CompanySalarySlipCategory $category)
    {
        if (!$category->trashed() && $category->delete()) {
            return $category;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanySalarySlipCategory(CompanySalarySlipCategory $category)
    {
        if ($category->trashed() && $category->restore()) {
            return $category;
        }
        return false;
    }
}
