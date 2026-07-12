<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class LocationWeekDTO
{
    /**
     * @param  list<ScheduleDayDTO>  $checkInWeek
     * @param  list<ScheduleDayDTO>  $appointmentWeek
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public string $timezoneAbbr,
        public bool $checkInEnabled,
        public bool $appointmentEnabled,
        public bool $isOpenNowCheckIn,
        public bool $isOpenNowAppointment,
        public array $checkInWeek,
        public array $appointmentWeek,
    ) {}
}
