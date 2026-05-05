<?php

declare(strict_types=1);

namespace App\Appointment;

use App\Address\AddressManager;
use App\DTOs\AppointmentLocationDTO;
use App\DTOs\LocationScheduleDTO;
use App\Models\Location;
use App\Queries\AppointmentLocations;
use App\Schedule\LocationScheduleParser;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final readonly class AppointmentScheduleResolver
{
    public function __construct(
        private AppointmentLocations $locationSchedules,
        private LocationScheduleParser $locationScheduleParser,
        private AddressManager $address,
    ) {}

    /**
     * @return Collection<int, AppointmentLocationDTO>
     */
    public function getLocations(): Collection
    {
        $now = now();

        return $this->locationSchedules->execute()
            ->map(fn (Location $location): AppointmentLocationDTO => $this->buildDTO($location, $now));
    }

    public function buildDTO(Location $location, CarbonInterface $now): AppointmentLocationDTO
    {
        $localNow = $now->setTimezone($location->timezone);
        $schedule = $this->resolveSchedule($location, $localNow);

        return new AppointmentLocationDTO(
            id: $location->uuid,
            name: $location->name,
            address: $this->address->buildAddress($location->address->toArray()),
            isOpen: $schedule->isOpen,
            todayOpenCloseTime: $this->formatOpenCloseTime($schedule),
            reason: $this->resolveReason($schedule),
            hasOverride: $schedule->hasOverride,
            isOverrideClosure: $schedule->isOverrideClosure,
            isClosingSoon: $this->isClosingSoon($schedule, $localNow),
        );
    }

    public function resolveSchedule(Location $location, CarbonInterface $at): LocationScheduleDTO
    {

        $localNow = $at->setTimezone($location->timezone);

        $override = $location->appointmentScheduleOverrides
            ->first(fn ($o): bool => $o->date === $localNow->toDateString());

        $schedule = $location->appointmentSchedule
            ->first(fn ($s): bool => $s->day_of_week === $localNow->dayOfWeek());

        if ($override) {
            return $this->locationScheduleParser->resolveFromOverride($override->toArray(), $location->timezone, $localNow);
        }

        return $this->locationScheduleParser->resolveFromSchedule($schedule?->toArray(), $location->timezone, $localNow);
    }

    private function formatOpenCloseTime(LocationScheduleDTO $schedule): string
    {
        return match (true) {
            $schedule->openTime && $schedule->closeTime => $schedule->openTime->format('g:i A').' - '.$schedule->closeTime->format('g:i A T'),
            ! $schedule->isOpen => __('messages.appointmentSelectLocation.closedToday'),
            default => 'N/A',
        };
    }

    private function resolveReason(LocationScheduleDTO $schedule): ?string
    {
        if (! $schedule->hasOverride) {
            return null;
        }

        if ($schedule->isOverrideClosure) {
            return $schedule->reason ?? __('messages.appointmentSelectLocation.closedToday');
        }

        return $schedule->reason ?? __('messages.appointmentSelectLocation.specialHoursToday');
    }

    private function isClosingSoon(LocationScheduleDTO $schedule, CarbonInterface $localNow): bool
    {
        return $schedule->isOpen
            && $schedule->closeTime instanceof CarbonInterface
            && $localNow->diffInMinutes($schedule->closeTime) <= config('app.location_closing_soon_threshold');
    }
}
