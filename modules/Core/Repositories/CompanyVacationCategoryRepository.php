<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Modules\Core\Models\CompanyVacationCategory;

trait CompanyVacationCategoryRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name', 'type', 'meta', 'grade_id'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyVacationCategory(array $data)
    {
        $category = new CompanyVacationCategory(
           Arr::only($data, $this->keys),
        );

        if($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyVacationCategory(CompanyVacationCategory $category, array $data)
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
    public function destroyCompanyVacationCategory(CompanyVacationCategory $category)
    {
        if(!$category->trashed() && $category->delete()) {
            return $category;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyVacationCategory(CompanyVacationCategory $category)
    {
        if($category->trashed() && $category->restore()) {
            return $category;
        }
        return false;
    }
}
