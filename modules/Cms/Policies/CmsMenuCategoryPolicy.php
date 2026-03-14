<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsMenuCategory;

class CmsMenuCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Can access page.
     */

    public function access(User $user)
    {
        return $user->hasAnyPermissionsTo(['read-menu-category', 'write-menu-category', 'delete-menu-category']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsMenuCategory $model)
    {
        return $user->hasAnyPermissionsTo(['read-menu-category']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-menu-category']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsMenuCategory $model)
    {
        return $user->hasAnyPermissionsTo(['write-menu-category']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsMenuCategory $model)
    {
        return $user->hasAnyPermissionsTo(['write-menu-category']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsMenuCategory $model)
    {
        return $user->hasAnyPermissionsTo(['delete-menu-category']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsMenuCategory $model)
    {
        return $user->hasAnyPermissionsTo(['delete-menu-category']);
    }
}
