<?php
declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;
use Carbon\CarbonInterface;

final readonly class LocationDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public bool $is_open,
        public ?string $today_open_close_time,
        public ?string $reason,
    ) {}


    /**
     * @param array<string, mixed> $location
     * @return self
     */
    public static function fromArray(array $location): self
    {
        [$isOpen, $openTime, $closeTime, $reason] = self::resolveOpenCloseTime($location);

        return new self(
            id: $location['uuid'],
            name: $location['name'],
            address: self::buildAddress($location['address']),
            is_open: $isOpen,
            today_open_close_time: $openTime && $closeTime
                ? $openTime->format('g:i A').' - '.$closeTime->format('g:i A T')
                : null,
            reason: $reason,
        );
    }

    /**
     * @param array<string, mixed> $location
     * @return array{bool, Carbon|null, Carbon|null, string|null}
     */
    private static function resolveOpenCloseTime(array $location): array
    {
        $timezone = $location['timezone'];
        $now = now()->setTimezone($timezone);

        $exceptions = $location['schedule_exceptions'] ?? [];
        $exception  = array_values(array_filter(
            $exceptions,
            fn (array $e) => $e['date'] === $now->toDateString()
        ))[0] ?? null;

        if ($exception) {
            return self::resolveFromException($exception, $timezone, $now);
        }

        return self::resolveFromSchedule($location['schedule'] ?? null, $timezone, $now);
    }

    /**
     * @param array<string, mixed> $exception
     * @return array{bool, Carbon|null, Carbon|null, string|null}
     */
    private static function resolveFromException(array $exception, string $timezone, CarbonInterface $now): array
    {
        if ($exception['is_closed']) {
            return [false, null, null, $exception['reason']];
        }

        $openTime = $exception['open'] ? Carbon::parse($exception['open'], $timezone) : null;
        $closeTime = $exception['close'] ? Carbon::parse($exception['close'], $timezone) : null;

        return [
            $openTime && $closeTime && $now->between($openTime, $closeTime),
            $openTime,
            $closeTime,
            $exception['reason'],
        ];
    }

    /**
     * @param ?array<string, mixed> $schedule
     * @return array{bool, Carbon|null, Carbon|null, string|null}
     */
    private static function resolveFromSchedule(?array $schedule, string $timezone, CarbonInterface $now): array
    {
        $day = strtolower($now->englishDayOfWeek);

        $openTime = isset($schedule["{$day}_open"]) ? Carbon::parse($schedule["{$day}_open"], $timezone) : null;
        $closeTime = isset($schedule["{$day}_close"]) ? Carbon::parse($schedule["{$day}_close"], $timezone) : null;

        return [
            $openTime && $closeTime && $now->between($openTime, $closeTime),
            $openTime,
            $closeTime,
            null,
        ];
    }

    /**
     * @param array<string, mixed> $address
     * @return string
     */
    private static function buildAddress(array $address): string
    {
        $parts = array_filter([
            $address['street1'],
            $address['street2'] ?? null,
            $address['city'],
            $address['state'],
        ]);

        return implode(', ', $parts).' '.$address['zip_code'];
    }
}
