<?php

namespace App\Http\Middleware;

use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class EnsureUserHasCoordinatesMiddleware
{
    public function __construct(
        private SessionService $session,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $userCoords = $this->session->getUserCoords();

        if (! $userCoords) {
            return to_route('checkIn.selectLocation')
                ->withErrors(['userCoords' => 'Please select a location to start over.']);
        }

        $request->attributes->set('userCoords', $userCoords);

        return $next($request);
    }
}
