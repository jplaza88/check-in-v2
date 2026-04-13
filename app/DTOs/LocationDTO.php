<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Services\LocationService;

final readonly class LocationDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public int $distance,
        public bool $isOpen,
        public ?string $todayOpenCloseTime,
        public ?string $reason,
        public bool $hasException,
        public bool $isExceptionClosure,
        public bool $isClosingSoon,
    ) {}

    /**
     * @param  array<string, mixed>  $location
     */
    public static function fromArray(array $location): self
    {
        $service = app(LocationService::class);

        [$isOpen, $openTime, $closeTime, $reason, $hasException, $isExceptionClosure] = $service->resolveOpenCloseTime($location);

        // TODO:: Convert to this to a feature for the DB
        $isClosingSoon = $isOpen
            && $closeTime !== null
            && now()->setTimezone($location['timezone'])->diffInMinutes($closeTime) <= config('location_closing_soon_threshold');

        return new self(
            id: $location['uuid'],
            name: $location['name'],
            address: $service->buildAddress($location['address']),
            distance: $location['distance'],
            isOpen: $isOpen,
            todayOpenCloseTime: $openTime && $closeTime
                ? $openTime->format('g:i A').' - '.$closeTime->format('g:i A T')
                : null,
            reason: $reason,
            hasException: $hasException,
            isExceptionClosure: $isExceptionClosure,
            isClosingSoon: $isClosingSoon,
        );
    }
}
