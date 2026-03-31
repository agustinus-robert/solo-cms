<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Models\CompanyOutworkCategory;

trait CompanyOutworkCategoryRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = ['name', 'description', 'price', 'meta', 'grade_id'];

    /**
     * Store newly created resource.
     */
    public function storeCompanyOutworkCategory(array $data)
    {
        $category = new CompanyOutworkCategory(
           Arr::only($data, $this->keys)
        );

        if ($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyOutworkCategory(CompanyOutworkCategory $category, array $data)
    {
        $category = $category->fill(
           Arr::only($data, $this->keys),
        );

        if ($category->save()) {
            return $category;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanyOutworkCategory(CompanyOutworkCategory $category)
    {
        if (!$category->trashed() && $category->delete()) {
            return $category;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyOutworkCategory(CompanyOutworkCategory $category)
    {
        if ($category->trashed() && $category->restore()) {
            return $category;
        }
        return false;
    }
}
