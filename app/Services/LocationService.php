<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Collection;

class LocationService
{
    /**
     * @return Collection<int, Location>
     */
    public function getActiveLocationsWithAddressAndSchedule(string $context): Collection
    {
        $whereClause = $context === 'checkin' ? 'is_checkins_enabled' : 'is_appointments_enabled';

        return Location::with(['address', 'schedule', 'scheduleExceptions'])
            ->where('is_active', true)
            ->where($whereClause, true)
            ->get();
    }
}
