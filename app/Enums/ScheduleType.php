<?php

declare(strict_types=1);

namespace App\Enums;

use Illuminate\Support\Str;

enum ScheduleType: string
{
    case CheckIn = 'checkin';
    case Appointment = 'appointment';

    public function label(): string
    {
        return match ($this) {
            self::CheckIn => __('Check-In'),
            self::Appointment => __('Appointment'),
        };
    }

    public function selectLocationPage(): string
    {
        return match ($this) {
            self::CheckIn => 'CheckInSelectLocation',
            self::Appointment => 'AppointmentSelectLocation',
        };
    }

    public function locationEnabledColumn(): string
    {
        return match ($this) {
            self::CheckIn => 'is_checkins_enabled',
            self::Appointment => 'is_appointments_enabled',
        };
    }

    /**
     * @return list<string>
     */
    public function scheduleRelationships(bool $snake = false): array
    {
        $relationships = match ($this) {
            self::CheckIn => ['checkInSchedule', 'checkInScheduleExceptions'],
            self::Appointment => ['appointmentSchedule', 'appointmentScheduleExceptions'],
        };

        return $snake
            ? array_map(Str::snake(...), $relationships)
            : $relationships;
    }

    public function isCheckIn(): bool
    {
        return $this === self::CheckIn;
    }
}
