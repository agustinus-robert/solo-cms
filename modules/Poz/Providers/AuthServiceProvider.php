<?php

namespace Modules\Poz\Providers;

use Modules\Admin\Models;
use Modules\Admin\Policies;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Account\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('poz::access', function ($user) {
            return $user->hasPermissionTo('view_pos');
        });

        Gate::define('supplier::access', function ($user) {
            return $user->hasPermissionTo('view_supplier');
        });
    }
}
