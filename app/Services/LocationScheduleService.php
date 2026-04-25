<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LocationScheduleDTO;
use App\Enums\ScheduleType;
use App\Models\Location;
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
     */
    public function resolveOpenCloseTime(array $location, ScheduleType $scheduleType, ?CarbonInterface $at = null): LocationScheduleDTO
    {
        $timezone = $location['timezone'];

        // Dynamically resolve schedule keys based on context (check-in vs appointment)
        [$scheduleKey, $exceptionsKey] = $scheduleType->scheduleRelationships(snake: true);
        $schedule = $location[$scheduleKey] ?? [];
        $scheduleExceptions = $location[$exceptionsKey] ?? [];

        // Check-in will use now(), appointments will use $at
        $dateTimeReference = ($at ?? now())->setTimezone($timezone);

        $scheduleException = array_values(array_filter(
            $scheduleExceptions,
            fn (array $e): bool => $e['date'] === $dateTimeReference->toDateString()
        ))[0] ?? null;

        if ($scheduleException) {
            return $this->resolveFromException($scheduleException, $timezone, $dateTimeReference);
        }

        return $this->resolveFromSchedule($schedule, $timezone, $dateTimeReference);
    }

    /**
     * @param  array<string, mixed>  $exception
     */
    private function resolveFromException(array $exception, string $timezone, CarbonInterface $now): LocationScheduleDTO
    {
        $openTime = $exception['open'] ? Date::parse($exception['open'], $timezone) : null;
        $closeTime = $exception['close'] ? Date::parse($exception['close'], $timezone) : null;

        if ($exception['is_closed']) {
            return new LocationScheduleDTO(
                isOpen: false,
                openTime: $openTime,
                closeTime: $closeTime,
                reason: $exception['reason'],
                hasException: true,
                isExceptionClosure: true,
            );
        }

        return new LocationScheduleDTO(
            isOpen: $openTime && $closeTime && $now->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
            reason: $exception['reason'],
            hasException: true
        );
    }

    /**
     * @param  ?array<string, mixed>  $schedule
     */
    private function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $now): LocationScheduleDTO
    {
        $day = mb_strtolower($now->englishDayOfWeek);

        $openTime = isset($schedule[$day.'_open']) ? Date::parse($schedule[$day.'_open'], $timezone) : null;
        $closeTime = isset($schedule[$day.'_close']) ? Date::parse($schedule[$day.'_close'], $timezone) : null;

        return new LocationScheduleDTO(
            isOpen: $openTime && $closeTime && $now->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime
        );
    }
}
