<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\CarbonImmutable;

final readonly class LocationScheduleDTO
{
    public function __construct(
        public bool $isOpen,
        public ?CarbonImmutable $openTime = null,
        public ?CarbonImmutable $closeTime = null,
        public ?string $reason = null,
        public bool $hasException = false,
        public bool $isExceptionClosure = false,
    ) {}
}
