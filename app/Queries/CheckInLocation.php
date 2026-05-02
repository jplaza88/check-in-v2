<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Location;

final class CheckInLocation
{
    public function execute(string $uuid): ?Location
    {
        return Location::with(['address', 'checkinSchedule'])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_checkins_enabled', true)
            ->first();
    }
}
