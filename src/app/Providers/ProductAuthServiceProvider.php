<?php

namespace VCComponent\Laravel\Product\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class ProductAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Gate::define('manage-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToUse');
        Gate::define('view-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToShow');
        Gate::define('create-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToCreate');
        Gate::define('update-item-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToUpdateItem');
        Gate::define('update-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToUpdate');
        Gate::define('delete-product', 'VCComponent\Laravel\Product\Contracts\ProductPolicyInterface@ableToDelete');
        //
    }
}
