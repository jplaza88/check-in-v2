<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Location;
use App\Queries\AppointmentLocation;
use App\Session\AppointmentSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final readonly class EnsureUserHasAppointmentLocationMiddleware
{
    public function __construct(
        private AppointmentSession $session,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $location = $this->session->location();

        if (! $location || ! $this->session->isFresh()) {
            $uuid = $request->route('uuid');

            if ($uuid && Str::isUuid($uuid)) {
                $location = resolve(AppointmentLocation::class)->execute($uuid);

                if (! $location instanceof Location) {
                    return $next($request);
                }
            }

            return to_route('appointment.selectLocation')
                ->withErrors(['uuid' => 'Please select a location to continue.']);
        }

        return $next($request);
    }
}
