<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Session\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureUserHasCoordinatesMiddleware
{
    public function __construct(private UserSession $session) {}

    public function handle(Request $request, Closure $next): Response
    {
        $userCoords = $this->session->getUserCoords();

        if (! $userCoords) {
            return to_route('checkIn.selectLocation')
                ->withErrors(['userCoords' => true]);
        }

        $request->attributes->set('userCoords', $userCoords);

        return $next($request);
    }
}
