<?php

namespace Modules\Cms\Providers;

use Modules\Cms\Models;
use Modules\Cms\Policies;
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
    protected $policies = [
        // Models\CmsCategory::class => Policies\CmsCategoryPolicy::class,
        // Models\CmsMenuCategory::class => Policies\CmsMenuCategoryPolicy::class,
        // Models\CmsMenuOrder::class => Policies\CmsMenuOrderPolicy::class,
        // Models\CmsPost::class => Policies\CmsPostPolicy::class,
        // Models\CmsSiteConfig::class => Policies\CmsSiteConfigPolicy::class,
        // Models\CmsTags::class => Policies\CmsTagsPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define(
            'cms::access',
            fn(User $user) => count(array_filter(array_map(fn($policy) => (new $policy())->access($user), $this->policies)))
        );

        // Gate::define(
        //     'client::access',
        //     fn (User $user) => count(array_filter(array_map(fn ($policy) => (new $policy())->access($user), $this->policies)))
        // );
    }
}
