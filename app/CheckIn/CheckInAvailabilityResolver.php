<?php

declare(strict_types=1);

namespace App\CheckIn;

use App\DTOs\CheckInAvailabilityDTO;
use App\DTOs\CheckInLocationScheduleDTO;
use App\Geo\DistanceCalculator;
use App\Models\Location;

final readonly class CheckInAvailabilityResolver
{
    public function __construct(
        private CheckInScheduleResolver $scheduleResolver,
        private DistanceCalculator $distance,
    ) {}

    /**
     * @param  array<mixed, mixed>  $userCoords
     */
    public function isAvailableForCheckIn(Location $location, array $userCoords): CheckInAvailabilityDTO
    {
        $userDistance = $this->distance->calculate(
            $userCoords['latitude'],
            $userCoords['longitude'],
            $location->address->latitude ?? 0.0,
            $location->address->longitude ?? 0.0
        );

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

        $localNow = now()->setTimezone($location->timezone);
        $schedule = $this->scheduleResolver->resolveSchedule($location, $localNow);

        return new CheckInAvailabilityDTO(
            allowed: $schedule->isOpen,
            reason: $this->resolveReason($location, $schedule)
        );
    }

    private function resolveReason(Location $location, CheckInLocationScheduleDTO $schedule): ?string
    {
        if ($schedule->isOpen) {
            return null;
        }

        // Admin-configured reason takes priority (e.g., "Closed for maintenance")
        if ($schedule->reason) {
            return __('messages.checkInSelectLocation.closedForCheckInWithReason', [
                'name' => $location->name,
                'reason' => $schedule->reason,
            ]);
        }

        // Schedule exception exists but admin didn't provide a reason - generate one

        if ($schedule->isExceptionClosure) {
            return __('messages.checkInSelectLocation.closedForCheckInWithoutReason', [
                'name' => $location->name,
            ]);
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
        return __('messages.checkInSelectLocation.closedForCheckInToday', ['name' => $location->name]);
    }
}
