<?php

declare(strict_types=1);

namespace App\Schedule;

use App\DTOs\LocationScheduleDTO;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;

final readonly class LocationScheduleParser
{
    /**
     * @param  array<string, mixed>  $override
     */
    public function resolveFromOverride(array $override, string $timezone, CarbonInterface $at): LocationScheduleDTO
    {
        $openTime = $override['open_time'] ? Date::parse($override['open_time'], $timezone) : null;
        $closeTime = $override['close_time'] ? Date::parse($override['close_time'], $timezone) : null;

        if ($override['is_closed']) {
            return new LocationScheduleDTO(
                isOpen: false,
                openTime: $openTime,
                closeTime: $closeTime,
                reason: $override['reason'],
                hasOverride: true,
                isOverrideClosure: true,
            );
        }

        return new LocationScheduleDTO(
            isOpen: $openTime && $closeTime && $at->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
            reason: $override['reason'],
            hasOverride: true,
            isOverrideClosure: false,
        );
    }

    /**
     * @param  ?array<string, mixed>  $schedule
     */
    public function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $at): LocationScheduleDTO
    {
        $openTime = $schedule && $schedule['open_time'] ? Date::parse($schedule['open_time'], $timezone) : null;
        $closeTime = $schedule && $schedule['close_time'] ? Date::parse($schedule['close_time'], $timezone) : null;

        return new LocationScheduleDTO(
            isOpen: $openTime && $closeTime && $at->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
        );
    }
}
