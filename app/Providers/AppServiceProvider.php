<?php

namespace App\Providers;

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
        if (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], '.test') || str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '127.0.0.1'))) {
            // Do not force HTTPS for local development
        } else {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
