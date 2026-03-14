<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsTags;

class CmsTagsPolicy
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
        return $user->hasAnyPermissionsTo(['read-tag', 'write-tag', 'delete-tag']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsTags $model)
    {
        return $user->hasAnyPermissionsTo(['read-tag']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-tag']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsTags $model)
    {
        return $user->hasAnyPermissionsTo(['write-tag']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsTags $model)
    {
        return $user->hasAnyPermissionsTo(['write-tag']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsTags $model)
    {
        return $user->hasAnyPermissionsTo(['delete-tag']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsTags $model)
    {
        return $user->hasAnyPermissionsTo(['delete-tag']);
    }
}
