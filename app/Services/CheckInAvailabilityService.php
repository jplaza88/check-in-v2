<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CheckInAvailabilityDTO;
use App\DTOs\LocationScheduleDTO;
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
        $userDistance = $this->calculateDistance(
            $userCoords['latitude'],
            $userCoords['longitude'],
            $location->address->latitude ?? 0.0,
            $location->address->longitude ?? 0.0
        );

        // Is user close enough?
        if ($userDistance >= $location->max_distance_allowed) {
            return new CheckInAvailabilityDTO(
                allowed: false,
                reason: __('messages.checkInSelectLocation.tooFar', [
                    'name' => $location->name,
                    'maxDistance' => $location->max_distance_allowed,
                    'userDistance' => $userDistance,
                ]),
            );
        }

        // Schedule & schedule exceptions
        $schedule = $this->resolveSchedule($location);

        return new CheckInAvailabilityDTO(
            allowed: $schedule->isOpen,
            reason: $this->resolveReason($location, $schedule)
        );
    }

    /**
     * Haversine distance formula
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
    {
        $earthRadius = 3958.8; // miles
        // $earthRadius = 6371; //km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));

        return round($earthRadius * $c, $decimalPlaces);
    }

    private function resolveSchedule(Location $location): LocationScheduleDTO
    {
        if (! isset($location->checkInSchedule, $location->checkInScheduleExceptions)) {
            return new LocationScheduleDTO(isOpen: false);
        }

        return $this->locationScheduleService
            ->resolveOpenCloseTime($location->toArray(), ScheduleType::CheckIn);
    }

    private function resolveReason(Location $location, LocationScheduleDTO $schedule): ?string
    {
        if ($schedule->isOpen) {
            return null;
        }

        // Admin-configured reason takes priority (e.g., "Closed for maintenance")
        if ($schedule->reason) {
            return $schedule->reason;
        }

        // Schedule exception exists but admin didn't provide a reason - generate one

        if ($schedule->isExceptionClosure) {
            return __('messages.checkInSelectLocation.closedToday');
        }

        if ($schedule->hasException && $schedule->openTime && $schedule->closeTime) {
            return __('messages.checkInSelectLocation.outsideExceptionHours', [
                'name' => $location->name,
                'open' => $schedule->openTime->format('g:i A'),
                'close' => $schedule->closeTime->format('g:i A T'),
            ]);
        }

        // No exception - outside regular business hours
        if ($schedule->openTime && $schedule->closeTime) {
            return __('messages.checkInSelectLocation.outsideHours', [
                'name' => $location->name,
                'open' => $schedule->openTime->format('g:i A'),
                'close' => $schedule->closeTime->format('g:i A T'),
            ]);
        }

        // No hours configured for today
        return __('messages.checkInSelectLocation.locationClosed');
    }
}
