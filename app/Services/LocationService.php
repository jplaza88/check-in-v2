<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class LocationService
{
    public function getActiveCheckInLocationsWithScheduleByUuid(string $uuid): ?Location
    {
        return Location::with(['address', 'schedule', 'scheduleExceptions'])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->where('is_checkins_enabled', true)
            ->first();
    }

    /**
     * @return Collection<int, Location>
     */
    public function getActiveLocationsWithAddressAndSchedule(string $context): Collection
    {
        $whereClause = $context === 'checkin' ? 'is_checkins_enabled' : 'is_appointments_enabled';

        return Location::with(['address', 'schedule', 'scheduleExceptions'])
            ->where('is_active', true)
            ->where($whereClause, true)
            ->get();
    }

    /**
     * @param  array{latitude: float, longitude: float}  $userCoords
     * @return array<string, mixed>
     */
    public function canCheckIn(Location $location, array $userCoords): array
    {
        // User Distance
        $distance = $this->distance(
            $userCoords['latitude'],
            $userCoords['longitude'],
            $location->latitude,
            $location->longitude
        );

        if ($distance > $location->distance) {
            return [
                'allowed' => false,
                'reason' => 'You are too far from this location.',
            ];
        }

        // Schedule
        if (! $this->isOpen($location)) {
            return [
                'allowed' => false,
                'reason' => 'This location is currently closed.',
            ];
        }

        return [
            'allowed' => true,
            'reason' => null,
        ];
    }

    public function isOpen(Location $location): bool
    {
        [$isOpen] = $this->resolveOpenCloseTime($location->toArray());

        return $isOpen;
    }

    /**
     * Haversine distance formula
     */
    public function distance(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
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

    /**
     * @param  array<string, mixed>  $location
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    public function resolveOpenCloseTime(array $location): array
    {
        $timezone = $location['timezone'];
        $now = now()->setTimezone($timezone);

        $exceptions = $location['schedule_exceptions'] ?? [];
        $exception = array_values(array_filter(
            $exceptions,
            fn (array $e) => $e['date'] === $now->toDateString()
        ))[0] ?? null;

        if ($exception) {
            return $this->resolveFromException($exception, $timezone, $now);
        }

        return $this->resolveFromSchedule($location['schedule'] ?? null, $timezone, $now);
    }

    /**
     * @param  array<string, mixed>  $address
     */
    public function buildAddress(array $address): string
    {
        $parts = array_filter([
            $address['street1'],
            $address['street2'] ?? null,
            $address['city'],
            $address['state'],
        ]);

        return implode(', ', $parts).' '.$address['zip_code'];
    }

    /**
     * @param  array<string, mixed>  $exception
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    private function resolveFromException(array $exception, string $timezone, CarbonInterface $now): array
    {
        $openTime = $exception['open'] ? Carbon::parse($exception['open'], $timezone) : null;
        $closeTime = $exception['close'] ? Carbon::parse($exception['close'], $timezone) : null;

        if ($exception['is_closed']) {
            return [false, $openTime, $closeTime, $exception['reason'], true, true];
        }

        return [
            $openTime && $closeTime && $now->between($openTime, $closeTime),
            $openTime,
            $closeTime,
            $exception['reason'],
            true,
            false,
        ];
    }

    /**
     * @param  ?array<string, mixed>  $schedule
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    private function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $now): array
    {
        $day = strtolower($now->englishDayOfWeek);

        $openTime = isset($schedule["{$day}_open"]) ? Carbon::parse($schedule["{$day}_open"], $timezone) : null;
        $closeTime = isset($schedule["{$day}_close"]) ? Carbon::parse($schedule["{$day}_close"], $timezone) : null;

        return [
            $openTime && $closeTime && $now->between($openTime, $closeTime),
            $openTime,
            $closeTime,
            null,
            false,
            false,
        ];
    }
}
