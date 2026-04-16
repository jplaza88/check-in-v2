<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\LocationDTO;
use App\Enums\ScheduleType;
use App\Models\Location;
use App\Services\LocationScheduleService;
use Illuminate\Http\Request;
use Inertia\Response;

class LocationSelectController extends Controller
{
    public function __invoke(Request $request, LocationScheduleService $service): Response
    {
        $context = $request->route('context');
        $scheduleType = $context === 'checkin' ? ScheduleType::CheckIn : ScheduleType::Appointment;

        $locations = $service->getActiveLocationsWithAddressAndSchedule($context)
            ->map(fn (Location $location) => LocationDTO::fromArray($location->toArray(), $scheduleType));

        $page = $context === 'checkin' ? 'CheckInSelectLocation' : 'AppointmentSelectLocation';

        return inertia($page, [
            'locations' => $locations,
            'context' => $context,
        ]);
    }
}
