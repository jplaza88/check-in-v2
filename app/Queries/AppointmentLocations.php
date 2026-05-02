<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;
use Illuminate\Support\Collection;

final readonly class AppointmentLocations
{
    /**
     * @return Collection<int, Location>
     */
    public function execute(): Collection
    {
        return Location::with(['address', 'appointmentSchedule', 'appointmentScheduleOverrides'])
            ->where('is_active', true)
            ->where('is_appointments_enabled', true)
            ->get();
    }
}
