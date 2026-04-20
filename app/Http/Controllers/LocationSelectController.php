<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CheckInSelectDTO;
use App\Enums\ScheduleType;
use App\Models\Location;
use App\Services\LocationScheduleService;
use Illuminate\Http\Request;
use Inertia\Response;

final class LocationSelectController extends Controller
{
    public function __invoke(Request $request, LocationScheduleService $service): Response
    {
        $scheduleType = ScheduleType::from($request->route('context'));

        $locations = $service->getActiveLocations($scheduleType)
            ->map(fn (Location $location): CheckInSelectDTO => CheckInSelectDTO::fromArray($location->toArray(), $scheduleType));

        return inertia($scheduleType->selectLocationPage(), [
            'locations' => $locations,
            'context' => $scheduleType->value,
        ]);
    }
}
