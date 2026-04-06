<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class SelectLocationController extends Controller
{
    public function __invoke(Request $request): Response
    {
        // Cache::forget('active_checkin_locations');

        $locations = collect(Cache::remember('active_checkin_locations', now()->endOfDay(),
            fn () => Location::with(['address', 'schedule'])
                ->where('is_active', true)
                ->where('is_checkins_enabled', true)
                ->get()
                ->toArray()
        ))->map(fn (array $location) => $this->formatLocation($location));

        return Inertia::render('SelectLocation', [
            'locations' => $locations,
            'context' => $request->route('context'),
        ]);
    }

    private function formatLocation(array $location): array
    {
        [$isOpen, $openTime, $closeTime] = $this->getOpenCloseTime($location);

        return [
            'id' => $location['uuid'],
            'name' => $location['name'],
            'address' => $this->buildAddress($location['address']),
            'is_open' => $isOpen,
            'open_close_time' => $openTime?->format('g:i A').' - '.$closeTime?->format('g:i A T'),
        ];
    }

    private function getOpenCloseTime(array $location): array
    {
        $timezone = $location['timezone'];
        $now = now()->setTimezone($timezone);
        $day = strtolower($now->englishDayOfWeek);
        $schedule = $location['schedule'] ?? null;

        $open = $schedule["{$day}_open"] ?? null;
        $close = $schedule["{$day}_close"] ?? null;
        $openTime = $open ? Carbon::parse($open, $timezone) : null;
        $closeTime = $close ? Carbon::parse($close, $timezone) : null;

        $isOpen = $openTime && $closeTime && $now->between($openTime, $closeTime);

        return [$isOpen, $openTime, $closeTime];
    }

    private function buildAddress(array $address): string
    {
        $parts = array_filter([
            $address['street1'],
            $address['street2'] ?? null,
            $address['city'],
            $address['state'],
        ]);

        return implode(', ', $parts);
    }
}
