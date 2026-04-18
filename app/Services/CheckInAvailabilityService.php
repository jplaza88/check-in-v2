<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CheckInAvailabilityDTO;
use App\Enums\ScheduleType;
use App\Models\Location;

final readonly class CheckInAvailabilityService
{
    public function __construct(private LocationScheduleService $locationScheduleService) {}

    /**
     * @param  array<mixed, mixed>  $userCoords
     */
    public function canCheckIn(Location $location, array $userCoords): CheckInAvailabilityDTO
    {
        // User Distance
        $distance = $this->calculateDistance(
            $userCoords['latitude'],
            $userCoords['longitude'],
            $location->latitude,
            $location->longitude
        );

        // Is user close enough?
        if ($distance > $location->max_distance_allowed) {
            return new CheckInAvailabilityDTO(
                allowed: false,
                reason: __('messages.checkInSelectLocation.tooFar'),
            );
        }

        // Schedule & schedule exceptions
        if (! $this->isOpen($location)) {
            return new CheckInAvailabilityDTO(
                allowed: false,
                reason: __('messages.checkInSelectLocation.locationClosed'),
            );
        }

        return new CheckInAvailabilityDTO(allowed: true);
    }

    /**
     * Haversine distance formula
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
    {
        $earthRadius = 3958.8; // miles
        // $earthRadius = 6371; //km

        $dlat = deg2rad($lat2 - $lat1);
        $dlng = deg2rad($lng2 - $lng1);
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;

        return round($earthRadius * 2 * asin(sqrt($a)), $decimalPlaces);
    }

    private function isOpen(Location $location): bool
    {
        if (! isset($location->checkInSchedule, $location->checkInScheduleExceptions)) {
            return false;
        }

        [$isOpen] = $this->locationScheduleService
            ->resolveOpenCloseTime($location->toArray(), ScheduleType::CheckIn);

        return $isOpen;
    }
}
