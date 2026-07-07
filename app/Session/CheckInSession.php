<?php

declare(strict_types=1);

namespace App\Session;

use App\DTOs\CheckInLocationDTO;

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
     * The resolved location DTO and form-field flags are stored alongside it so
     * the (potentially long) form can be rendered without re-querying, while the
     * pass authorizes completion without re-validating live coordinates.
     *
     * @param  array<string, bool>  $fields
     */
    public function issueGatePass(CheckInLocationDTO $location, array $fields): void
    {
        session([
            self::GATE_PASS_KEY => [
                'uuid' => $location->id,
                'location' => $location,
                'fields' => $fields,
                'storedAt' => now()->timestamp,
            ],
        ]);
    }

    public function hasFreshGatePass(string $locationUuid): bool
    {
        $pass = $this->freshGatePass();

        return $pass !== null && $pass['uuid'] === $locationUuid;
    }

    /**
     * The location resolved at the gate, or null once the pass has expired.
     * Mirrors AppointmentSession::getLocation().
     */
    public function getLocation(): ?CheckInLocationDTO
    {
        $location = $this->freshGatePass()['location'] ?? null;

        return $location instanceof CheckInLocationDTO ? $location : null;
    }

    /**
     * The form-field flags resolved at the gate, or null once the pass has
     * expired.
     *
     * @return array<string, bool>|null
     */
    public function getCheckInFormFields(): ?array
    {
        return $this->freshGatePass()['fields'] ?? null;
    }

    public function forgetGatePass(): void
    {
        session()->forget(self::GATE_PASS_KEY);
    }

    /**
     * The stored gate pass if it exists and is within its TTL, otherwise null.
     * Expired passes are forgotten so freshness is folded into every read.
     *
     * @return array{uuid: string, location: CheckInLocationDTO, fields: array<string, bool>, storedAt: int}|null
     */
    private function freshGatePass(): ?array
    {
        $pass = session(self::GATE_PASS_KEY);

        if (! $pass) {
            return null;
        }

        if (now()->timestamp - $pass['storedAt'] > config('app.checkin_gate_pass_ttl')) {
            $this->forgetGatePass();

            return null;
        }

        return $pass;
    }

    public function forgetUserCoords(): void
    {
        session()->forget(self::USER_COORDINATES_KEY);
    }
}
