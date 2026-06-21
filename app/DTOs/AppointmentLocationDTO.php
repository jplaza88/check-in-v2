<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class AppointmentLocationDTO
{
    /**
     * @param  list<AppointmentAvailabilityWindowDTO>  $availability
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public bool $isOpen,
        public ?string $todayOpenCloseTime,
        public ?string $reason,
        public bool $hasOverride,
        public bool $isOverrideClosure,
        public ?bool $isClosingSoon,
        public array $availability,
        public int $slotIntervalMinutes,
    ) {}
}
