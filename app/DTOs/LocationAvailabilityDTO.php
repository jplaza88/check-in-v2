<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class LocationAvailabilityDTO
{
    public function __construct(public bool $allowed, public ?string $reason = null) {}
}
