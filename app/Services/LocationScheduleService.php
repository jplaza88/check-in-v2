<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final readonly class LocationScheduleService
{
    public function getActiveCheckInLocationsWithScheduleByUuid(string $uuid): ?Location
    {
        return Location::with(['address', 'checkInSchedule', 'checkInScheduleExceptions'])
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

        return Location::with(['address', 'checkInSchedule', 'checkInScheduleExceptions'])
            ->where('is_active', true)
            ->where($whereClause, true)
            ->get();
    }

    /**
     * @param  array<string, array<string, string>>  $schedule
     * @param  array<string, array<string, string>>  $exceptions
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    public function resolveOpenCloseTime(array $schedule, array $exceptions, string $timezone, ?CarbonInterface $targetDateTime = null): array
    {
        // TODO:: Handle $targetDateTime for appointments

        $now = now()->setTimezone($timezone);

        $exception = array_values(array_filter(
            $exceptions,
            fn (array $e) => $e['date'] === $now->toDateString()
        ))[0] ?? null;

        if ($exception) {
            return $this->resolveFromException($exception, $timezone, $now);
        }

        return $this->resolveFromSchedule($schedule, $timezone, $now);
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
