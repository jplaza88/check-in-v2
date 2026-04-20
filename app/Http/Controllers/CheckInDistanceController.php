<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\LocationDistanceDTO;
use App\Enums\ScheduleType;
use App\Http\Requests\CheckInDistanceRequest;
use App\Models\Location;
use App\Services\CheckInAvailabilityService;
use App\Services\LocationScheduleService;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;

/**
 * JSON endpoint:
 *
 * Provides distances from the user's coordinates to each active check-in location.
 *
 * Used by the check-in select location flow: {@see CheckInSelectController} renders the page,
 * then the client requests this endpoint so the UI can sort or annotate locations by distance.
 *
 * Only locations enabled for check-in are included ({@see ScheduleType::CheckIn}).
 */
final class CheckInDistanceController extends Controller
{
    public function __invoke(
        CheckInDistanceRequest $request
    ): JsonResponse {
        $userLat = $request->float('latitude');
        $userLng = $request->float('longitude');

        $locationService = resolve(LocationScheduleService::class);
        $checkInAvailabilityService = resolve(CheckInAvailabilityService::class);
        $sessionService = resolve(SessionService::class);

        $distances = $locationService->getActiveLocations(ScheduleType::CheckIn)
            ->map(fn (Location $location): LocationDistanceDTO => new LocationDistanceDTO(
                id: $location->uuid,
                userDistance: $checkInAvailabilityService->calculateDistance(
                    $userLat, $userLng,
                    (float) $location->address->latitude,
                    (float) $location->address->longitude,
                ),
            ))
            ->sortBy('userDistance')
            ->values();

        $sessionService->setUserCoords($userLat, $userLng);

        return response()->json(['locations' => $distances]);
    }
}
