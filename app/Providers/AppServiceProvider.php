<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Stripe\StripeClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- AJOUT IMPORTANT 1

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // <--- AJOUT IMPORTANT 2 : On force le HTTPS en Prod
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        // Limiter à 2 jobs simultanés pour le traitement PDF
        \Illuminate\Support\Facades\RateLimiter::for('pdf-processing', function ($job) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(2);
        });
    }
}
