<?php

declare(strict_types=1);

namespace App\Session;

use App\DTOs\AppointmentLocationDTO;

final readonly class AppointmentSession
{
    private const string KEY = 'appointmentLocationContext';

    /**
     * @return array<string, string>
     */
    public function getLocation(): array
    {
        return session(self::KEY.'.location') ?? [];
    }

    public function getStoredAt(): ?int
    {
        $value = session(self::KEY.'.storedAt');

        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    public function setLocation(AppointmentLocationDTO $location): void
    {
        session([self::KEY => [
            'location' => $location,
            'storedAt' => now()->timestamp,
        ]]);
    }

    public function forget(): void
    {
        session()->forget(self::KEY);
    }

    public function isFresh(): bool
    {
        $storedAt = $this->getStoredAt();

        if (is_null($storedAt)) {
            $this->forget();

            return false;
        }

        $storedSeconds = $storedAt;
        $ttl = (int) config('app.user_appointment_location_context_ttl');

        return (int) now()->timestamp - $storedSeconds <= $ttl;
    }
}
