<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;
use Illuminate\Support\Collection;

final readonly class ScheduleLocations
{
    /**
     * Active locations that offer check-in and/or appointments, with every
     * schedule relation eager-loaded so the weekly resolver stays N+1 free.
     *
     * @return Collection<int, Location>
     */
    public function execute(): Collection
    {
        return Location::with([
            'address',
            'checkinSchedule',
            'checkinScheduleOverrides',
            'appointmentSchedule',
            'appointmentScheduleOverrides',
        ])
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->where('is_checkins_enabled', true)
                    ->orWhere('is_appointments_enabled', true);
            })
            ->orderBy('name')
            ->get();
    }
}
