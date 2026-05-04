<?php

declare(strict_types=1);

namespace App\Session;

use App\Models\Location;

final readonly class CheckInSession
{
    /**
     * @return array{latitude: float, longitude: float, stored_at: int}|null
     */
    public function getUserCoords(): ?array
    {
        $coords = session()->has('userCoords') ? session('userCoords') : null;

        /*if (! $coords) {
            return null;
        }*/

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
}
