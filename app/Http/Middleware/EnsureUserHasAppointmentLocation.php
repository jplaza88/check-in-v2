<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Location;
use App\Queries\AppointmentLocation;
use App\Session\AppointmentSession;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class EnsureUserHasAppointmentLocation
{
    public function __construct(
        private AppointmentSession $session,
    ) {}

    public function handle(Request $request, Closure $next): RedirectResponse
    {
        $location = $this->session->getLocation();

        // The user either doesn't have a location in session
        // or the session is stale, let's force
        if (! $location || ! $this->session->isFresh()) {
            $uuid = $request->route('uuid');

            if ($uuid && Str::isUuid($uuid)) {
                $location = resolve(AppointmentLocation::class)->execute($uuid);

                if (! $location instanceof Location) {
                    return $next($request);
                }
            }

            // TODO:: Add translation
            return to_route('appointment.selectLocation')
                ->withErrors(['uuid' => 'Please select a location to continue.']);
        }

        return $next($request);
    }
}
