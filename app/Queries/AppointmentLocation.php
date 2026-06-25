<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;

final class AppointmentLocation
{
    public function execute(string $uuid, bool $firstOrFail = false): ?Location
    {
        $location = Location::with(['address', 'appointmentSchedule', 'appointmentScheduleOverrides'])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_appointments_enabled', true);

        return $firstOrFail ? $location->firstOrFail() : $location->first();
    }
}
