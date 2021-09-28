<?php

namespace VCComponent\Laravel\Product\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use VCComponent\Laravel\Product\Contracts\ProductPolicyInterface;
use VCComponent\Laravel\Product\Entities\Product;

class ProductAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Product::class => ProductPolicyInterface::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //
    }
}
