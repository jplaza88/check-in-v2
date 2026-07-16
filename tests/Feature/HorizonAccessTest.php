<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravel\Sentinel\Http\Middleware\SentinelMiddleware;

it('does not guard the horizon route group with sentinel middleware', function (): void {
    $middleware = Route::getMiddlewareGroups()['horizon'];

    expect($middleware)->not->toContain(SentinelMiddleware::class.':horizon');
});
