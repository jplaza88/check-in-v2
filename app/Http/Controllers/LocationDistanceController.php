<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\LocationDistanceDTO;
use App\Enums\ScheduleType;
use App\Http\Requests\LocationDistanceRequest;
use App\Services\CheckInAvailabilityService;
use App\Services\LocationScheduleService;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;

final class LocationDistanceController extends Controller
{
    public function __invoke(
        LocationDistanceRequest $request
    ): JsonResponse {
        $userLat = $request->float('latitude');
        $userLng = $request->float('longitude');

        $locationService = resolve(LocationScheduleService::class);
        $checkInAvailabilityService = resolve(CheckInAvailabilityService::class);
        $sessionService = resolve(SessionService::class);

        $distances = $locationService->getActiveLocations(ScheduleType::CheckIn)
            ->filter(function ($location): bool {
                $valid = ! is_null($location->address->latitude) && ! is_null($location->address->longitude);

                if (! $valid) {
                    logger()->warning('Location missing address coordinates, excluded from distance calculation.', [
                        'location_id' => $location->id,
                        'location_name' => $location->name,
                    ]);
                }

                return $valid;
            })
            ->map(fn ($location): LocationDistanceDTO => new LocationDistanceDTO(
                id: $location->uuid,
                userDistance: $checkInAvailabilityService->distance(
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
