<?php

declare(strict_types=1);

use App\Http\Middleware\AuthTimebox;
use App\Http\Middleware\EnsureRegistrationAllowed;
use App\Http\Middleware\EnsureUserHasAppointmentGatePass;
use App\Http\Middleware\EnsureUserHasCheckInGatePass;
use App\Http\Middleware\EnsureUserHasCoordinates;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware(['web', 'setLocale'])
                ->group(base_path('routes/auth.php'));

            // Last, so the bare /{code} form cannot shadow a real page.
            Route::middleware('throttle:60,1')
                ->group(base_path('routes/shortlink.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'setLocale' => SetLocale::class,
            'auth.timebox' => AuthTimebox::class,
            'userCoordinates' => EnsureUserHasCoordinates::class,
            'appointmentGatePass' => EnsureUserHasAppointmentGatePass::class,
            'checkInGatePass' => EnsureUserHasCheckInGatePass::class,
            'registrationAllowed' => EnsureRegistrationAllowed::class,
        ]);

        // We're behind Caddy
        $middleware->trustProxies(at: '*');
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
