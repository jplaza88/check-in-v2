<?php

declare(strict_types=1);

namespace App\Session;

final readonly class CheckInSession
{
    /**
     * @return array{latitude: float, longitude: float, stored_at: int}|null
     */
    public function getUserCoords(): ?array
    {
        $coords = session('userCoords');

        if (! $coords) {
            return null;
        }

        // Expire after 2 minutes - mirrors the localStorage browser TTL
        if (now()->timestamp - $coords['storedAt'] > config('app.user_coordinates_session_ttl')) {
            session()->forget('userCoords');

            return null;
        }

        return $coords;
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
}
