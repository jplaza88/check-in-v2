<?php

namespace App\Enums;

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
}
