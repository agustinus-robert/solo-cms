<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsCategory;

class CmsCategoryPolicy
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
        return $user->hasAnyPermissionsTo(['read-category', 'write-category', 'delete-category']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsCategory $model)
    {
        return $user->hasAnyPermissionsTo(['read-category']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-category']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsCategory $model)
    {
        return $user->hasAnyPermissionsTo(['write-category']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsCategory $model)
    {
        return $user->hasAnyPermissionsTo(['write-category']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsCategory $model)
    {
        return $user->hasAnyPermissionsTo(['delete-category']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, Menu $model)
    {
        return $user->hasAnyPermissionsTo(['delete-categories']);
    }
}
