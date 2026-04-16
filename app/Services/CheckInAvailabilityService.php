<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;

final readonly class CheckInAvailabilityService
{
    public function __construct(private LocationScheduleService $locationScheduleService) {}

    /**
     * @param  array<mixed, mixed>  $userCoords
     * @return array<string, mixed>
     */
    public function canCheckIn(Location $location, array $userCoords): array
    {
        // User Distance
        $distance = $this->distance(
            $userCoords['latitude'],
            $userCoords['longitude'],
            $location->latitude,
            $location->longitude
        );

        // Is user close enough?
        if ($distance > $location->max_distance_allowed) {
            return [
                'allowed' => false,
                'reason' => 'You are too far from this location.',
            ];
        }

        // Schedule & schedule exceptions
        if (! $this->isOpen($location)) {
            return [
                'allowed' => false,
                'reason' => 'This location is currently closed.',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
        ];
    }

    /**
     * Haversine distance formula
     */
    public function distance(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
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
            ->resolveOpenCloseTime(
                $location->checkInSchedule->toArray(),
                $location->checkInScheduleExceptions->toArray(),
                $location->timezone
            );

        return $isOpen;
    }
}
