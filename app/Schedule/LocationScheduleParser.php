<?php

declare(strict_types=1);

namespace App\Schedule;

use App\DTOs\LocationScheduleDTO;
use Carbon\CarbonInterface;

final readonly class LocationScheduleParser
{
    /**
     * @param  array<string, mixed>  $override
     */
    public function resolveFromOverride(array $override, string $timezone, CarbonInterface $at): LocationScheduleDTO
    {
        $openTime = $override['open_time'] ? $at->copy()->setTimeFromTimeString($override['open_time']) : null;
        $closeTime = $override['close_time'] ? $at->copy()->setTimeFromTimeString($override['close_time']) : null;

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
        $openTime = $schedule && $schedule['open_time'] ? $at->copy()->setTimeFromTimeString($schedule['open_time']) : null;
        $closeTime = $schedule && $schedule['close_time'] ? $at->copy()->setTimeFromTimeString($schedule['close_time']) : null;

        return new LocationScheduleDTO(
            isOpen: $openTime && $closeTime && $at->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
        );
    }
}
