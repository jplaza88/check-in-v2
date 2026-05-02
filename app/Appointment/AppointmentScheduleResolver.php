<?php

declare(strict_types=1);

namespace App\Appointment;

use App\Address\AddressManager;
use App\DTOs\AppointmentLocationScheduleDTO;
use App\DTOs\AppointmentLocationSelectDTO;
use App\Models\Location;
use App\Queries\AppointmentLocations;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final readonly class AppointmentScheduleResolver
{
    public function __construct(
        private AppointmentLocations $locationSchedules,
        private AddressManager $address,
    ) {}

    /**
     * @return Collection<int, AppointmentLocationSelectDTO>
     */
    public function getLocations(): Collection
    {
        $now = now();

        return $this->locationSchedules->execute()
            ->map(fn (Location $location): AppointmentLocationSelectDTO => $this->buildDTO($location, $now));
    }

    public function buildDTO(Location $location, CarbonInterface $now): AppointmentLocationSelectDTO
    {
        $localNow = $now->setTimezone($location->timezone);
        $schedule = $this->resolveSchedule($location, $localNow);

        return new AppointmentLocationSelectDTO(
            id: $location->uuid,
            name: $location->name,
            address: $this->address->buildAddress($location->address->toArray()),
            isOpen: $schedule->isOpen,
            todayOpenCloseTime: $this->formatOpenCloseTime($schedule),
            reason: $this->resolveReason($schedule),
            hasException: $schedule->hasException,
            isExceptionClosure: $schedule->isExceptionClosure,
            isClosingSoon: $this->isClosingSoon($schedule, $localNow),
        );
    }

    public function resolveSchedule(Location $location, CarbonInterface $at): AppointmentLocationScheduleDTO
    {

        $localNow = $at->setTimezone($location->timezone);
        // dd($localNow->toDateString());

        $override = $location->appointmentScheduleOverrides
            ->first(fn ($o): bool => $o->date === $localNow->toDateString());

        $schedule = $location->appointmentSchedule
            ->first(fn ($s): bool => $s->day_of_week === $localNow->dayOfWeek());

        if ($override) {
            return $this->resolveFromOverride($override->toArray(), $location->timezone, $localNow);
        }

        return $this->resolveFromSchedule($schedule?->toArray(), $location->timezone, $localNow);
    }

    /**
     * @param  array<string, mixed>  $override
     */
    private function resolveFromOverride(array $override, string $timezone, CarbonInterface $at): AppointmentLocationScheduleDTO
    {
        $openTime = $override['open_time'] ? Date::parse($override['open_time'], $timezone) : null;
        $closeTime = $override['close_time'] ? Date::parse($override['close_time'], $timezone) : null;

        if ($override['is_closed']) {
            return new AppointmentLocationScheduleDTO(
                isOpen: false,
                openTime: $openTime,
                closeTime: $closeTime,
                reason: $override['reason'],
                hasException: true,
                isExceptionClosure: true,
            );
        }

        return new AppointmentLocationScheduleDTO(
            isOpen: $openTime && $closeTime && $at->between($openTime, $closeTime),
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
    private function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $at): AppointmentLocationScheduleDTO
    {
        $openTime = $schedule && $schedule['open_time'] ? Date::parse($schedule['open_time'], $timezone) : null;
        $closeTime = $schedule && $schedule['close_time'] ? Date::parse($schedule['close_time'], $timezone) : null;

        return new AppointmentLocationScheduleDTO(
            isOpen: $openTime && $closeTime && $at->between($openTime, $closeTime),
            openTime: $openTime,
            closeTime: $closeTime,
        );
    }

    private function formatOpenCloseTime(AppointmentLocationScheduleDTO $schedule): string
    {
        return match (true) {
            $schedule->openTime && $schedule->closeTime => $schedule->openTime->format('g:i A').' - '.$schedule->closeTime->format('g:i A T'),
            ! $schedule->isOpen => __('messages.checkInSelectLocation.closedToday'),
            default => 'N/A',
        };
    }

    private function resolveReason(AppointmentLocationScheduleDTO $schedule): ?string
    {
        if (! $schedule->hasException) {
            return null;
        }

        if ($schedule->isExceptionClosure) {
            return $schedule->reason ?? __('messages.appointmentSelectLocation.closedToday');
        }

        return $schedule->reason ?? __('messages.appointmentSelectLocation.specialHoursToday');
    }

    private function isClosingSoon(AppointmentLocationScheduleDTO $schedule, CarbonInterface $localNow): bool
    {
        return $schedule->isOpen
            && $schedule->closeTime instanceof CarbonInterface
            && $localNow->diffInMinutes($schedule->closeTime) <= config('app.location_closing_soon_threshold');
    }
}
