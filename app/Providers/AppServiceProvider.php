<?php

namespace App\Providers;

use App\API\TroubleFree;
use App\API\WooCommerce;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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
        try {
            TroubleFree::initialize();
            WooCommerce::initialize();
        } catch(\Illuminate\Database\QueryException $e) {
            // Do nothing
        } catch (\Exception $e) {
            // Do nothing
        }

        Gate::define('viewPulse', function () {
            return auth()->check();
        });    
    }
}
