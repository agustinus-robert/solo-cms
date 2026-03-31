<?php

namespace Modules\Core\Repositories;

use Arr;
use Auth;
use Modules\Core\Models\CompanyStudentLeaveCategory;

trait CompanyStudentLeaveCategoryRepository
{
    /**
     * Define the form keys for resource
     */
    private $keys = [
        'name',
        'parent_id',
        'meta',
        'grade_id'
    ];

    /**
     * Store newly created resource.
     */
    public function storeCompanyLeaveCategory(array $data)
    {
        $category = new CompanyStudentLeaveCategory(
           Arr::only($data, $this->keys),
        );

        if ($category->save()) {
            Auth::user()->log('membuat kategori izin baru dengan nama ' . $category->name . ' <strong>[ID: ' . $category->id . ']</strong>', CompanyStudentLeaveCategory::class, $category->id);
            return $category;
        }
        return false;
    }

    /**
     * Update the current resource.
     */
    public function updateCompanyLeaveCategory(CompanyStudentLeaveCategory $category, array $data)
    {
        $category = $category->fill(
           Arr::only($data, $this->keys),
        );

        if ($category->save()) {
            Auth::user()->log('memperbarui kategori izin ' . $category->name . ' <strong>[ID: ' . $category->id . ']</strong>', CompanyStudentLeaveCategory::class, $category->id);
            return $category;
        }
        return false;
    }

    /**
     * Remove the current resource.
     */
    public function destroyCompanyLeaveCategory(CompanyStudentLeaveCategory $category)
    {
        if (!$category->trashed() && $category->delete()) {
            Auth::user()->log('menghapus kategori izin ' . $category->name . ' <strong>[ID: ' . $category->id . ']</strong>', CompanyStudentLeaveCategory::class, $category->id);
            return $category;
        }
        return false;
    }

    /**
     * Restore the current resource.
     */
    public function restoreCompanyLeaveCategory(CompanyStudentLeaveCategory $category)
    {
        if ($category->trashed() && $category->restore()) {
            Auth::user()->log('memulihkan kategori izin ' . $category->name . ' <strong>[ID: ' . $category->id . ']</strong>', CompanyStudentLeaveCategory::class, $category->id);
            return $category;
        }
        return false;
    }
}
