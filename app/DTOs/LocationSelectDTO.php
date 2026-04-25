<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ScheduleType;
use App\Services\AddressService;
use App\Services\LocationScheduleService;

final readonly class LocationSelectDTO
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

    /**
     * @param  array<string, mixed>  $location
     */
    public static function fromArray(array $location, ScheduleType $scheduleType): self
    {
        $locationService = resolve(LocationScheduleService::class);
        $addressService = resolve(AddressService::class);

        $schedule = $locationService->resolveOpenCloseTime($location, $scheduleType);

        // TODO:: Convert to this to an admin feature
        if ($scheduleType->isCheckIn()) {
            $isClosingSoon = $schedule->isOpen
                && $schedule->closeTime !== null
                && now()->setTimezone($location['timezone'])->diffInMinutes($schedule->closeTime) <= config('app.location_closing_soon_threshold');
        }

        return new self(
            id: $location['uuid'],
            name: $location['name'],
            address: $addressService->buildAddress($location['address']),
            maxDistanceAllowed: $location['max_distance_allowed'],
            isOpen: $schedule->isOpen,
            todayOpenCloseTime: self::formatOpenCloseTime($schedule),
            reason: self::resolveReason($schedule),
            hasException: $schedule->hasException,
            isExceptionClosure: $schedule->isExceptionClosure,
            isClosingSoon: $isClosingSoon ?? null,
        );
    }

    private static function formatOpenCloseTime(LocationScheduleDTO $schedule): string
    {
        return match (true) {
            $schedule->openTime && $schedule->closeTime => $schedule->openTime->format('g:i A').' - '.$schedule->closeTime->format('g:i A T'),
            ! $schedule->isOpen => __('messages.checkInSelectLocation.closedToday'),
            default => 'N/A',
        };
    }

    private static function resolveReason(LocationScheduleDTO $schedule): ?string
    {
        if (! $schedule->hasException) {
            return null;
        }

        if ($schedule->isExceptionClosure) {
            return $schedule->reason ?? __('messages.checkInSelectLocation.closedToday');
        }

        return $schedule->reason ?? __('messages.checkInSelectLocation.specialHoursToday');
    }
}
