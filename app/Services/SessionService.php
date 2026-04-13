<?php

namespace App\Services;

class SessionService
{
    public function getLocale(): ?string
    {
        return session('locale');
    }

    /**
     * @return array{latitude: float, longitude: float, stored_at: int}|null
     */
    public function getUserCoords(): ?array
    {
        $coords = session('userCoords');

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
}
