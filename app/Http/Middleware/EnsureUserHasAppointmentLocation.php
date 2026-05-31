<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Appointment\AppointmentScheduleResolver;
use App\Models\Location;
use App\Queries\AppointmentLocation;
use App\Session\AppointmentSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureUserHasAppointmentLocation
{
    public function __construct(
        private AppointmentSession $session,
        private AppointmentScheduleResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $uuid = $request->route('uuid');

        if (! is_string($uuid)) {
            return $this->redirectToSelectLocation();
        }

        // Already cleared the gate (or refreshed within the TTL).
        if ($this->session->hasFreshLocation($uuid)) {
            return $next($request);
        }

        if (Str::isUuid($uuid)) {
            $location = resolve(AppointmentLocation::class)->execute($uuid);

            if ($location instanceof Location) {
                $this->session->setLocation($this->resolver->buildDTO($location, now()));

                return $next($request);
            }
        }

        return $this->redirectToSelectLocation();
    }

    private function redirectToSelectLocation(): Response
    {
        return to_route('appointment.selectLocation')
            ->withErrors(['uuid' => __('messages.appointmentSelectLocation.selectLocationToContinue')]);
    }
}
