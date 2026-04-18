<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ScheduleType;
use App\Services\AddressService;
use App\Services\LocationScheduleService;

final readonly class LocationDTO
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
        public bool $isClosingSoon,
    ) {}

    /**
     * @param  array<string, mixed>  $location
     */
    public static function fromArray(array $location, ScheduleType $scheduleType): self
    {
        $locationService = resolve(LocationScheduleService::class);
        $addressService = resolve(AddressService::class);

        [$isOpen, $openTime, $closeTime, $reason, $hasException, $isExceptionClosure] =
            $locationService->resolveOpenCloseTime($location, $scheduleType);

        // TODO:: Convert to this to a feature for the DB
        $isClosingSoon = $isOpen
            && $closeTime !== null
            && now()->setTimezone($location['timezone'])->diffInMinutes($closeTime) <= config('app.location_closing_soon_threshold');

        return new self(
            id: $location['uuid'],
            name: $location['name'],
            address: $addressService->buildAddress($location['address']),
            maxDistanceAllowed: $location['max_distance_allowed'],
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
