<?php

declare(strict_types=1);

namespace App\Session;

final readonly class CheckInSession
{
    private const string GATE_PASS_KEY = 'checkInGatePass';

    private const string USER_COORDINATES_KEY = 'userCoordinates';

    /**
     * @return array{latitude: float, longitude: float, stored_at: int}|null
     */
    public function getUserCoords(): ?array
    {
        $coords = session(self::USER_COORDINATES_KEY);

        if (! $coords) {
            return null;
        }

        // Expire after 2 minutes - mirrors the localStorage browser TTL
        if (now()->timestamp - $coords['storedAt'] > config('app.user_coordinates_session_ttl')) {
            $this->forgetUserCoords();

            return null;
        }

        return $coords;
    }

    /**
     * Persist the driver's coordinates for the select-location flow.
     *
     * Flow: the backend renders the select-location page with the available
     * locations, then the front-end sends an AJAX request with the driver's
     * coordinates. That request calls this method to store the coordinates,
     * computes the distance from each location, and returns them so the
     * front-end can sort the list by distance for the driver to choose from.
     */
    public function setUserCoords(float $latitude, float $longitude): void
    {
        session([
            self::USER_COORDINATES_KEY => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'storedAt' => now()->timestamp,
            ],
        ]);
    }

    /**
     * Issue a gate pass for a location once the proximity check has passed.
     * This authorizes form completion without re-validating live coordinates.
     */
    public function issueGatePass(string $locationUuid): void
    {
        session([
            self::GATE_PASS_KEY => [
                'uuid' => $locationUuid,
                'storedAt' => now()->timestamp,
            ],
        ]);
    }

    public function hasFreshGatePass(string $locationUuid): bool
    {
        $pass = session(self::GATE_PASS_KEY);

        if (! $pass || ($pass['uuid'] ?? null) !== $locationUuid) {
            return false;
        }

        if (now()->timestamp - $pass['storedAt'] > config('app.checkin_gate_pass_ttl')) {
            $this->forgetGatePass();

            return false;
        }

        return true;
    }

    public function forgetGatePass(): void
    {
        session()->forget(self::GATE_PASS_KEY);
    }

    public function forgetUserCoords(): void
    {
        session()->forget(self::USER_COORDINATES_KEY);
    }
}
