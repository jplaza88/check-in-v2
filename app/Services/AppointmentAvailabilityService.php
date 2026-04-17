<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Location;
use Carbon\CarbonInterface;

final readonly class AppointmentAvailabilityService
{
    /*public function __construct(private LocationScheduleService $localeScheduleService) {}

    public function isAvailableForAppointment(Location $location, CarbonInterface $dateTime): bool
    {

        $this->localeScheduleService->resolveOpenCloseTime(
            $location->appointmentSchedule->toArray(),
            $location->appointmentScheduleExceptions->toArray(),
            $location->timezone,
            $dateTime
        );

        return true;
    }*/
}
