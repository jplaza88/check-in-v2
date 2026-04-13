<?php

namespace App\Http\Controllers;

use App\DTOs\LocationDistanceDTO;
use App\Http\Requests\LocationDistanceRequest;
use App\Services\LocationService;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;

class LocationDistanceController extends Controller
{
    public function __invoke(
        LocationDistanceRequest $request
    ): JsonResponse {
        $userLat = $request->float('latitude');
        $userLng = $request->float('longitude');

        $locationService = app(LocationService::class);
        $sessionService = app(SessionService::class);

        $distances = $locationService->getActiveLocationsWithAddressAndSchedule('checkin')
            ->filter(function ($location) {
                $valid = ! is_null($location->address->latitude) && ! is_null($location->address->longitude);

                if (! $valid) {
                    logger()->warning('Location missing address coordinates, excluded from distance calculation.', [
                        'location_id' => $location->id,
                        'location_name' => $location->name,
                    ]);
                }

                return $valid;
            })
            ->map(fn ($location) => new LocationDistanceDTO(
                id: $location->uuid,
                userDistance: $locationService->distance(
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
