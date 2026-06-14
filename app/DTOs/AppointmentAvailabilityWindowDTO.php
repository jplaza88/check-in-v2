<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class AppointmentAvailabilityWindowDTO
{
    public function __construct(
        public string $date,
        public string $firstSlot,
        public string $lastSlot,
    ) {}
}
