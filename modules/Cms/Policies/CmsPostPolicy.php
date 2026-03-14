<?php

namespace Modules\Cms\Policies;

use Modules\Account\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Cms\Models\CmsPost;

class CmsPostPolicy
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
        return $user->hasAnyPermissionsTo(['read-cms-posts', 'write-cms-posts', 'delete-cms-posts']);
    }

    /**
     * Can show.
     */
    public function show(User $user, CmsPost $model)
    {
        return $user->hasAnyPermissionsTo(['read-cms-posts']);
    }

    /**
     * Can store.
     */
    public function store(User $user)
    {
        return $user->hasAnyPermissionsTo(['write-cms-posts']);
    }

    /**
     * Can update.
     */
    public function update(User $user, CmsPost $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-posts']);
    }


    /**
     * Can destroy.
     */
    public function destroy(User $user, CmsPost $model)
    {
        return $user->hasAnyPermissionsTo(['write-cms-posts']);
    }

    /**
     * Can restore.
     */
    public function restore(User $user, CmsPost $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-posts']);
    }

    /**
     * Can kill.
     */
    public function kill(User $user, CmsPost $model)
    {
        return $user->hasAnyPermissionsTo(['delete-cms-posts']);
    }
}
