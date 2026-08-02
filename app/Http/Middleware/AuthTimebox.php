<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Timebox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Forces sensitive auth endpoints (login, password reset link request) to take a
 * constant minimum time, so an attacker cannot infer whether an account exists
 * from response timing. Pairs with throttling and a uniform response body.
 *
 * The floor is configurable via fortify.auth_timebox_microseconds and is set to
 * zero in the test environment so the suite never actually sleeps.
 */
final class AuthTimebox
{
    /**
     * Route names whose responses are held to the constant-time floor.
     *
     * @var list<string>
     */
    private const array SENSITIVE_ROUTES = ['login', 'password.email'];

    /**
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): Response
    {
        $microseconds = (int) config('fortify.auth_timebox_microseconds', 300000);

        if ($microseconds <= 0 || ! in_array($request->route()?->getName(), self::SENSITIVE_ROUTES, true)) {
            return $next($request);
        }

        return (new Timebox)->call(static fn (): Response => $next($request), $microseconds);
    }
}
