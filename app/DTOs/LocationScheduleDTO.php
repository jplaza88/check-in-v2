<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\CarbonInterface;

final readonly class LocationScheduleDTO
{
    public function __construct(
        public bool $isOpen,
        public ?CarbonInterface $openTime = null,
        public ?CarbonInterface $closeTime = null,
        public ?string $reason = null,
        public bool $hasException = false,
        public bool $isExceptionClosure = false,
    ) {}
}
