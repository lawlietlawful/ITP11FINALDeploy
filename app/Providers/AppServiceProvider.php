<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // ─── Rate Limiters ───

        // Login: max 5 attempts per minute per IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Too many login attempts. Please try again in a minute.',
                    ]);
                });
        });

        // Public document request submission: max 30 per minute per IP
        RateLimiter::for('public-request', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(function () {
                    return back()
                        ->withInput()
                        ->withErrors(['throttle' => 'Too many requests submitted. Please try again later.']);
                });
        });

        // Public tracking lookups: max 30 per minute per IP
        RateLimiter::for('public-track', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'tracking_code' => 'Too many tracking attempts. Please try again in a minute.',
                    ]);
                });
        });
    }
}
