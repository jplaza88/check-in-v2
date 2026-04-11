<?php

namespace App\Http\Controllers;

use App\DTOs\LocationDistanceDTO;
use App\Http\Requests\LocationDistanceRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationDistanceController extends Controller
{
    public function __invoke(LocationDistanceRequest $request, LocationService $service): JsonResponse
    {
        $userLat = $request->float('latitude');
        $userLng = $request->float('longitude');

        $distances = $service->getActiveLocationsWithAddressAndSchedule('checkin')
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
                distance: $this->haversine(
                    $userLat, $userLng,
                    (float) $location->address->latitude,
                    (float) $location->address->longitude,
                ),
            ))
            ->sortBy('distance')
            ->values();

        return response()->json(['locations' => $distances]);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
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
}
