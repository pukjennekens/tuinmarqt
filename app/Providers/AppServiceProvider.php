<?php

namespace App\Providers;

use App\API\TroubleFree;
use App\API\WooCommerce;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TroubleFree::initialize();
        WooCommerce::initialize();
    }
}
