<?php

namespace App\Http\Controllers;

use App\DTOs\LocationDTO;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class SelectLocationController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Cache::forget('active_checkin_locations');
        Cache::forget('active_appointment_locations');

        $context = $request->route('context');
        $cacheKey = "active_{$context}_locations";
        $whereClause = $context === 'checkin' ? 'is_checkins_enabled' : 'is_appointments_enabled';

        $locations = collect(Cache::remember($cacheKey, now()->endOfDay(),
            fn () => Location::with(['address', 'schedule', 'scheduleExceptions'])
                ->where('is_active', true)
                ->where($whereClause, true)
                ->get()
                ->toArray()
        ))->map(fn (array $location) => LocationDTO::fromArray($location));

        return Inertia::render('SelectLocation', [
            'locations' => $locations,
            'context' => $context,
        ]);
    }
}
