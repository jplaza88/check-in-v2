<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ScheduleType;
use App\Models\Location;
use Carbon\CarbonInterface;

final readonly class AppointmentAvailabilityService
{
    /*public function __construct(private LocationScheduleService $localeScheduleService) {}

    public function isAvailableForAppointment(Location $location, CarbonInterface $dateTime): bool
    {

        $this->localeScheduleService->resolveOpenCloseTime($location->toArray(), ScheduleType::Appointment);

        return true;
    }*/
}
