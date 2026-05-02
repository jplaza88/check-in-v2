<?php

declare(strict_types=1);

namespace App\Session;

use App\Models\Location;

final readonly class UserSession
{
    public function getId(): string
    {
        return session()->getId();
    }

    public function getLocale(): ?string
    {
        return session()->has('locale') ? session('locale') : null;
    }

    /**
     * @return array{latitude: float, longitude: float, stored_at: int}|null
     */
    public function getUserCoords(): ?array
    {
        $coords = session()->has('userCoords') ? session('userCoords') : null;

        if (! $coords) {
            return null;
        }

        // Expire after 30 minutes - mirrors the localStorage browser TTL
        if (now()->timestamp - $coords['storedAt'] > config('app.user_coordinates_session_ttl')) {
            session()->forget('userCoords');

            return null;
        }

        return $coords;
    }

    public function getCheckInLocation(): ?Location
    {
        return session()->has('checkInLocation') ? session('checkInLocation') : null;
    }

    public function getAppointmentLocation(): array
    {
        return session()->has('appointmentLocation') ? session('appointmentLocation') : [];
    }

    public function setLocale(string $locale): void
    {
        session(['locale' => $locale]);
    }

    public function setUserCoords(float $latitude, float $longitude): void
    {
        session([
            'userCoords' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'storedAt' => now()->timestamp,
            ],
        ]);
    }

    public function setCheckInLocation(?Location $location): void
    {
        session([
            'checkInLocation' => $location,
        ]);
    }

    public function setAppointmentLocation(?Location $location): void
    {
        session([
            'appointmentLocation' => $location,
        ]);
    }
}
