<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\LocationAvailabilityDTO;
use App\DTOs\LocationScheduleDTO;
use App\Enums\ScheduleType;
use App\Models\Location;
use Carbon\CarbonInterface;

final readonly class AppointmentAvailabilityService
{
    public function __construct(private LocationScheduleService $locationScheduleService) {}

    public function isAvailableForAppointment(Location $location, CarbonInterface $appointmentDateTime): LocationAvailabilityDTO
    {
        $schedule = $this->resolveSchedule($location, $appointmentDateTime);

        return new LocationAvailabilityDTO(
            allowed: $schedule->isOpen,
            reason: $this->resolveReason($location, $schedule, $appointmentDateTime)
        );
    }

    private function resolveSchedule(Location $location, CarbonInterface $appointmentDateTime): LocationScheduleDTO
    {
        if (! isset($location->appointmentSchedule, $location->appointmentScheduleExceptions)) {
            return new LocationScheduleDTO(isOpen: false);
        }

        return $this->locationScheduleService->resolveOpenCloseTime(
            $location->toArray(),
            ScheduleType::Appointment,
            $appointmentDateTime
        );
    }

    private function resolveReason(Location $location, LocationScheduleDTO $schedule, CarbonInterface $appointmentDateTime): ?string
    {
        if ($schedule->isOpen) {
            return null;
        }

        // Admin-configured reason takes priority (e.g., "Closed for maintenance")
        if ($schedule->reason) {
            return __('messages.appointmentBooking.closedForAppointmentsOnDateWithReason', [
                'name' => $location->name,
                'date' => $appointmentDateTime->format('m/d/Y'),
                'reason' => $schedule->reason,
            ]);
        }

        // Schedule exception exists but admin didn't provide a reason - generate one

        if ($schedule->isExceptionClosure) {
            return __('messages.appointmentBooking.closedForAppointmentsOnDateWithoutReason', [
                'name' => $location->name,
                'date' => $appointmentDateTime->format('m/d/Y'),
            ]);
        }

        if ($schedule->hasException && $schedule->openTime && $schedule->closeTime) {
            return __('messages.appointmentBooking.outsideSpecialHours', [
                'name' => $location->name,
                'time' => $appointmentDateTime->format('g:i A T'),
                'date' => $appointmentDateTime->format('m/d/Y'),
                'open' => $schedule->openTime->format('g:i A'),
                'close' => $schedule->closeTime->format('g:i A T'),
            ]);
        }

        // No exception - outside regular business hours
        if ($schedule->openTime && $schedule->closeTime) {
            return __('messages.appointmentBooking.outsideRegularHours', [
                'name' => $location->name,
                'time' => $appointmentDateTime->format('g:i A T'),
                'date' => $appointmentDateTime->format('m/d/Y'),
                'open' => $schedule->openTime->format('g:i A T'),
                'close' => $schedule->closeTime->format('g:i A T'),
            ]);
        }

        // No hours configured for date requested
        return __('messages.appointmentBooking.notAvailableOnDay', [
            'name' => $location->name,
            'date' => $appointmentDateTime->format('m/d/Y'),
        ]);
    }
}
