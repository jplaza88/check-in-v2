<?php

declare(strict_types=1);

namespace App\Session;

use App\DTOs\AppointmentLocationDTO;

final readonly class AppointmentSession
{
    private const string LOCATION_KEY = 'appointmentLocationContext';

    /**
     * Store the location selected at the appointment gate. Mirrors the
     * check-in gate pass: it proves the user cleared the gate and is bound to
     * a specific location, with a TTL bounding how long the booking form stays
     * accessible.
     */
    public function setLocation(AppointmentLocationDTO $location): void
    {
        session([
            self::LOCATION_KEY => [
                'location' => $location,
                'storedAt' => now()->timestamp,
            ],
        ]);
    }

    /**
     * The selected location, or null once the context has expired. Freshness
     * is folded into the getter to mirror CheckInSession::getUserCoords().
     */
    public function getLocation(): ?AppointmentLocationDTO
    {
        $context = session(self::LOCATION_KEY);

        if (! $context || ! ($context['location'] ?? null) instanceof AppointmentLocationDTO) {
            return null;
        }

        if (now()->timestamp - $context['storedAt'] > config('app.user_appointment_location_context_ttl')) {
            $this->forgetLocation();

            return null;
        }

        return $context['location'];
    }

    /**
     * Whether a fresh context exists for the given location. Mirrors
     * CheckInSession::hasFreshGatePass().
     */
    public function hasFreshLocation(string $locationUuid): bool
    {
        $location = $this->getLocation();

        return $location instanceof AppointmentLocationDTO && $location->id === $locationUuid;
    }

    public function forgetLocation(): void
    {
        session()->forget(self::LOCATION_KEY);
    }
}
