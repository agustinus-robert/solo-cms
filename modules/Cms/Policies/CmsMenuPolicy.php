<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsMenu;

class CmsMenuPolicy
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
        return $user->hasAnyPermissionsTo(['read-cms-menus', 'write-cms-menus', 'delete-cms-menus']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsMenu $model)
    {
        return $user->hasAnyPermissionsTo(['read-cms-menus']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menus']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsMenu $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menus']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsMenu $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menus']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsMenu $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-menus']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsMenu $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-menus']);
    }
}
