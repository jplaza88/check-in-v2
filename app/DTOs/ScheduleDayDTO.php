<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class ScheduleDayDTO
{
    public function __construct(
        public string $weekday,
        public string $shortWeekday,
        public int $dayOfMonth,
        public bool $isToday,
        public ?string $hours,
        public bool $hasOverride,
        public bool $isOverrideClosure,
        public ?string $reason,
    ) {}
}
