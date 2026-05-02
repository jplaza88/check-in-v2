<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class CheckInLocationSelectDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public int $maxDistanceAllowed,
        public bool $isOpen,
        public ?string $todayOpenCloseTime,
        public ?string $reason,
        public bool $hasException,
        public bool $isExceptionClosure,
        public ?bool $isClosingSoon,
    ) {}
}
