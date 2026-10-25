<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Limit per email+IP and per IP so both targeted guessing and
        // credential stuffing from one address are throttled.
        RateLimiter::for('login', fn(Request $request) => [
            Limit::perMinute(5)->by(
                strtolower((string) $request->input('email')) . '|' . $request->ip()
            ),
            Limit::perMinute(20)->by($request->ip()),
        ]);
    }
}
