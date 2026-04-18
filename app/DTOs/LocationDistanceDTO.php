<?php

declare(strict_types=1);

namespace App\DTOs;

use JsonSerializable;

final readonly class LocationDistanceDTO implements JsonSerializable
{
    public function __construct(public string $id, public float $userDistance) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userDistance' => $this->userDistance,
        ];
    }
}
