<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;

final class AppointmentLocation
{
    public function execute(string $uuid): ?Location
    {
        return Location::with(['address', 'appointmentSchedule'])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_appointments_enabled', true)
            ->first();
    }
}
