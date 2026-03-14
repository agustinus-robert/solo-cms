<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsMenuOrder;

class CmsMenuOrderPolicy
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
        return $user->hasAnyPermissionsTo(['read-cms-menu-orders', 'write-cms-menu-orders', 'delete-cms-menu-orders']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsMenuOrder $model)
    {
        return $user->hasAnyPermissionsTo(['read-cms-menu-orders']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menu-orders']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsMenuOrder $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menu-orders']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsMenuOrder $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-menu-orders']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsMenuOrder $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-menu-orders']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsMenuOrder $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-menu-orders']);
    }
}
