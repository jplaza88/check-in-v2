<?php

declare(strict_types=1);

namespace App\Session;

use App\DTOs\AppointmentLocationSelectDTO;
use App\Models\Location;

final readonly class AppointmentSession
{
    private const string KEY = 'appointmentLocationContext';

    public function set(Location $location, AppointmentLocationSelectDTO $scheduleToday): void
    {
        session([self::KEY => [
            'location' => $location,
            'scheduleToday' => $scheduleToday,
            'storedAt' => now()->timestamp,
        ]]);
    }

    public function location(): array
    {
        return session(self::KEY . '.location') ?? [];
    }

    public function scheduleToday(): array
    {
        return session(self::KEY . '.scheduleToday') ?? [];
    }

    public function storedAt(): float|int|string|null
    {
        return session(self::KEY . '.storedAt');
    }

    public function forget(): void
    {
        session()->forget(self::KEY);
    }

    public function isFresh(): bool
    {
        if ( is_null($this->storedAt()) ) {
            return false;
        }

        return now()->timestamp - $this->storedAt() <= 120;
    }
}
