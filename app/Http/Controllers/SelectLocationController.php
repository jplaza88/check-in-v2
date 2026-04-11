<?php

namespace App\Http\Controllers;

use App\DTOs\LocationDTO;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SelectLocationController extends Controller
{
    public function __invoke(Request $request, LocationService $service): Response
    {
        $context = $request->route('context');

        $locations = $service->getActiveLocationsWithAddressAndSchedule($context)
            ->map(fn (Location $location) => LocationDTO::fromArray($location->toArray()));

        $page = $context === 'checkin' ? 'CheckInSelectLocation' : 'AppointmentSelectLocation';

        return Inertia::render($page, [
            'locations' => $locations,
            'context' => $context,
        ]);
    }
}
