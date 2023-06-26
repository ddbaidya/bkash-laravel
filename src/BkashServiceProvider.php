<?php

namespace Ddbaidya\BkashLaravel;

use Illuminate\Support\ServiceProvider;

class BkashServiceProvider extends ServiceProvider
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
        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/bkash.php' => config_path('bkash.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        }
    }
}
