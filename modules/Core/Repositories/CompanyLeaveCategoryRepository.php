<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Modules\Core\Models\CompanyLeaveCategory;

trait CompanyLeaveCategoryRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name', 'grade_id', 'parent_id', 'meta'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyLeaveCategory(array $data)
    {
        $category = new CompanyLeaveCategory(
            Arr::only($data, $this->keys)
        );

        if($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyLeaveCategory(CompanyLeaveCategory $category, array $data)
    {
        $category = $category->fill(
           Arr::only($data, $this->keys),
        );

        if($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanyLeaveCategory(CompanyLeaveCategory $category)
    {
        if(!$category->trashed() && $category->delete()) {
            return $category;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyLeaveCategory(CompanyLeaveCategory $category)
    {
        if($category->trashed() && $category->restore()) {
            return $category;
        }
        return false;
    }
}
