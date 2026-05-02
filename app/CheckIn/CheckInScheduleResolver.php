<?php

declare(strict_types=1);

namespace App\CheckIn;

use App\Address\AddressManager;
use App\DTOs\CheckInLocationScheduleDTO;
use App\DTOs\CheckInLocationSelectDTO;
use App\Models\Location;
use App\Queries\CheckInLocations;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class CheckInScheduleResolver
{
    public function __construct(
        private CheckInLocations $locationSchedules,
        private AddressManager $address,
    ) {}

    /**
     * @return Collection<int, CheckInLocationSelectDTO>
     */
    public function getLocations(): Collection
    {
        return $this->locationSchedules->execute()
            ->map(fn (Location $location): CheckInLocationSelectDTO => $this->buildDTO($location, now()));
    }

    public function buildDTO(Location $location, CarbonInterface $now): CheckInLocationSelectDTO
    {
        $localNow = $now->setTimezone($location->timezone);
        $schedule = $this->resolveSchedule($location, $localNow);

        return new CheckInLocationSelectDTO(
            id: $location->uuid,
            name: $location->name,
            address: $this->address->buildAddress($location->address->toArray()),
            maxDistanceAllowed: $location->max_distance_allowed,
            isOpen: $schedule->isOpen,
            todayOpenCloseTime: $this->formatOpenCloseTime($schedule),
            reason: $this->resolveReason($schedule),
            hasException: $schedule->hasException,
            isExceptionClosure: $schedule->isExceptionClosure,
            isClosingSoon: $this->isClosingSoon($schedule, $localNow),
        );
    }

    public function resolveSchedule(Location $location, CarbonInterface $now): CheckInLocationScheduleDTO
    {
        $localNow = $now->setTimezone($location->timezone);

        $override = $location->checkinScheduleOverrides
            ->first(fn ($o): bool => $o->date === $localNow->toDateString());

        $schedule = $location->checkinSchedule
            ->first(fn ($s): bool => $s->day_of_week === $localNow->dayOfWeek());

        if ($override) {
            return $this->resolveFromOverride($override->toArray(), $location->timezone, $localNow);
        }

        return $this->resolveFromSchedule($schedule?->toArray(), $location->timezone, $localNow);
    }

    /**
     * @param  array<string, mixed>  $override
     */
    private function resolveFromOverride(array $override, string $timezone, CarbonInterface $now): CheckInLocationScheduleDTO
    {
        $openTime = $override['open_time'] ? Date::parse($override['open_time'], $timezone) : null;
        $closeTime = $override['close_time'] ? Date::parse($override['close_time'], $timezone) : null;

        if ($override['is_closed']) {
            return new CheckInLocationScheduleDTO(
                isOpen: false,
                openTime: $openTime,
                closeTime: $closeTime,
                reason: $override['reason'],
                hasException: true,
                isExceptionClosure: true,
            );
        }

        return new CheckInLocationScheduleDTO(
            isOpen: $openTime && $closeTime && $now->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
            reason: $override['reason'],
            hasException: true,
            isExceptionClosure: false,
        );
    }

    /**
     * @param  ?array<string, mixed>  $schedule
     */
    private function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $now): CheckInLocationScheduleDTO
    {
        $openTime = $schedule && $schedule['open_time'] ? Date::parse($schedule['open_time'], $timezone) : null;
        $closeTime = $schedule && $schedule['close_time'] ? Date::parse($schedule['close_time'], $timezone) : null;

        return new CheckInLocationScheduleDTO(
            isOpen: $openTime && $closeTime && $now->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
        );
    }

    private function formatOpenCloseTime(CheckInLocationScheduleDTO $schedule): string
    {
        return match (true) {
            $schedule->openTime && $schedule->closeTime => $schedule->openTime->format('g:i A').' - '.$schedule->closeTime->format('g:i A T'),
            ! $schedule->isOpen && ! $schedule->openTime && ! $schedule->closeTime => __('messages.checkInSelectLocation.closedToday'),
            default => 'N/A',
        };
    }

    private function resolveReason(CheckInLocationScheduleDTO $schedule): ?string
    {
        if (! $schedule->hasException) {
            return null;
        }

        if ($schedule->isExceptionClosure) {
            return $schedule->reason ?? __('messages.checkInSelectLocation.closedToday');
        }

        return $schedule->reason ?? __('messages.checkInSelectLocation.specialHoursToday');
    }

    private function isClosingSoon(CheckInLocationScheduleDTO $schedule, CarbonInterface $localNow): bool
    {
        return $schedule->isOpen
            && $schedule->closeTime instanceof CarbonInterface
            && $localNow->diffInMinutes($schedule->closeTime) <= config('app.location_closing_soon_threshold');
    }
}
