<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ScheduleType;
use App\Models\Location;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class LocationScheduleService
{
    public function getActiveLocationByUuid(string $uuid, ScheduleType $scheduleType): ?Location
    {
        return Location::with(['address', ...$scheduleType->scheduleRelationships()])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->where($scheduleType->locationEnabledColumn(), true)
            ->first();
    }

    /**
     * @return Collection<int, Location>
     */
    public function getActiveLocations(ScheduleType $scheduleType): Collection
    {
        return Location::with(['address', ...$scheduleType->scheduleRelationships()])
            ->where('is_active', true)
            ->where($scheduleType->locationEnabledColumn(), true)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $location
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    public function resolveOpenCloseTime(array $location, ScheduleType $scheduleType): array
    {
        $timezone = $location['timezone'];

        // Dynamically resolve schedule keys based on context (check-in vs appointment)
        [$scheduleKey, $exceptionsKey] = $scheduleType->scheduleRelationships(snake: true);
        $schedule = $location[$scheduleKey] ?? [];
        $scheduleExceptions = $location[$exceptionsKey] ?? [];

        $now = now()->setTimezone($timezone);

        $scheduleException = array_values(array_filter(
            $scheduleExceptions,
            fn (array $e): bool => $e['date'] === $now->toDateString()
        ))[0] ?? null;

        if ($scheduleException) {
            return $this->resolveFromException($scheduleException, $timezone, $now);
        }

        return $this->resolveFromSchedule($schedule, $timezone, $now);
    }

    /**
     * @param  array<string, mixed>  $exception
     * @return array{bool, Carbon|null, Carbon|null, string|null, bool, bool}
     */
    private function resolveFromException(array $exception, string $timezone, CarbonInterface $now): array
    {
        $openTime = $exception['open'] ? Date::parse($exception['open'], $timezone) : null;
        $closeTime = $exception['close'] ? Date::parse($exception['close'], $timezone) : null;

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

        $openTime = isset($schedule[$day.'_open']) ? Date::parse($schedule[$day.'_open'], $timezone) : null;
        $closeTime = isset($schedule[$day.'_close']) ? Date::parse($schedule[$day.'_close'], $timezone) : null;

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
