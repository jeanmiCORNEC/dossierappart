<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        // Limiter à 2 jobs simultanés pour le traitement PDF
        \Illuminate\Support\Facades\RateLimiter::for('pdf-processing', function ($job) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(2);
        });
    }
}
