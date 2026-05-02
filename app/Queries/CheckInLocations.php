<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;
use Illuminate\Support\Collection;

final readonly class CheckInLocations
{
    /**
     * @return Collection<int, Location>
     */
    public function execute(): Collection
    {
        return Location::with(['address', 'checkinSchedule', 'checkinScheduleOverrides'])
            ->where('is_active', true)
            ->where('is_checkins_enabled', true)
            ->get();
    }
}
