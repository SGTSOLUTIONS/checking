<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GeoDataService;

class GeoDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the GeoDataService as singleton
        $this->app->singleton(GeoDataService::class, function ($app) {
            return new GeoDataService();
        });

        // Register alias for convenience
        $this->app->alias(GeoDataService::class, 'geo-data');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // You can publish config file if needed
        // $this->publishes([
        //     __DIR__.'/../../config/geodata.php' => config_path('geodata.php'),
        // ], 'geodata-config');
    }
}