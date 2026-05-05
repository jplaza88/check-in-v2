<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CheckInLocationDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public int $maxDistanceAllowed,
        public bool $isOpen,
        public ?string $todayOpenCloseTime,
        public ?string $reason,
        public bool $hasOverride,
        public bool $isOverrideClosure,
        public ?bool $isClosingSoon,
    ) {}
}
