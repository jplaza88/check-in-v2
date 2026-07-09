<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Session\CheckInSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The gate pass is issued after the proximity + schedule checks pass at the
 * gate. Unlike the appointment location, it cannot be re-derived from the
 * route uuid alone, so an expired pass sends the driver back to re-validate.
 */
final readonly class EnsureUserHasCheckInGatePass
{
    public function __construct(private CheckInSession $session) {}

    public function handle(Request $request, Closure $next): Response
    {
        $uuid = $request->route('uuid');

        if (is_string($uuid) && $this->session->hasFreshGatePass($uuid)) {
            return $next($request);
        }

        return to_route('checkIn.selectLocation')
            ->withErrors(['uuid' => __('messages.checkInSelectLocation.selectLocationToContinue')]);
    }
}
