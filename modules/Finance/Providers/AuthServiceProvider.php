<?php

namespace Modules\Finance\Providers;

use Modules\HRMS\Policies as HRMSPolicy;
use Modules\Core\Policies as CorePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('finance::access', function ($user) {
            return true;
        });
    }
}
