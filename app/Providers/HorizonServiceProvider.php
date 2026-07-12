<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Horizon\HorizonApplicationServiceProvider;

final class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        $this->removeSentinelMiddleware();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', fn ($user = null): bool => $user?->email === 'admin@juanplaza.dev');
    }

    /**
     * Re-register the "horizon" route group without Laravel Sentinel's middleware.
     *
     * Horizon 5.47+ prepends SentinelMiddleware to the group. Its "laravel" driver
     * only guards the local environment, and behind our FrankenPHP/Caddy proxy the
     * forwarded client IP is public, so it treats this machine as an exposed local
     * app and aborts with 401 before the viewHorizon gate below can run. Sentinel is
     * a no-op outside local, so stripping it here leaves production access fully
     * governed by the gate.
     */
    private function removeSentinelMiddleware(): void
    {
        $this->app->booted(function (): void {
            Route::middlewareGroup('horizon', config('horizon.middleware', ['web']));
        });
    }
}
