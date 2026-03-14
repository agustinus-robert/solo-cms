<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsSiteConfig;

class CmsSiteConfigPolicy
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
        return $user->hasAnyPermissionsTo(['read-site-config', 'write-site-config', 'delete-site-config']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsSiteConfig $model)
    {
        return $user->hasAnyPermissionsTo(['read-site-config']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-site-config']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsSiteConfig $model)
    {
        return $user->hasAnyPermissionsTo(['write-site-config']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsSiteConfig $model)
    {
        return $user->hasAnyPermissionsTo(['write-site-config']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsSiteConfig $model)
    {
        return $user->hasAnyPermissionsTo(['delete-site-config']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsSiteConfig $model)
    {
        return $user->hasAnyPermissionsTo(['delete-site-config']);
    }
}
