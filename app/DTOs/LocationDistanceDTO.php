<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class LocationDistanceDTO implements \JsonSerializable
{
    public function __construct(public string $id, public float $distance) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'distance' => $this->distance,
        ];
    }
}
