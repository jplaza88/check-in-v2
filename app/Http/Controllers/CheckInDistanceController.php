<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\CheckIn\CheckInDistanceCalculator;
use App\Http\Requests\CheckInDistanceRequest;
use App\Session\CheckInSession;
use Illuminate\Http\JsonResponse;

/**
 * JSON endpoint:
 *
 * Provides distances from the user's coordinates to each active check-in location.
 *
 * Used by the check-in select location flow: {@see CheckInSelectController} renders the page,
 * then the client requests this endpoint so the UI can sort or annotate locations by distance.
 */
final class CheckInDistanceController extends Controller
{
    public function __invoke(
        CheckInDistanceRequest $request
    ): JsonResponse {
        $userLat = $request->float('latitude');
        $userLng = $request->float('longitude');

        $distance = resolve(CheckInDistanceCalculator::class);
        $session = resolve(CheckInSession::class);

        $distances = $distance->resolve($userLat, $userLng);

        $session->setUserCoords($userLat, $userLng);

        return response()->json(['locations' => $distances]);
    }
}
