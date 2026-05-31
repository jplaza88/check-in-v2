<?php

declare(strict_types=1);

use App\CheckIn\CheckInDistanceCalculator;
use App\DTOs\CheckInDistanceDTO;
use App\Models\Address;
use App\Models\Location;
use App\Session\UserSession;

it('returns a DTO per location with its uuid and computed distance', function (): void {
    $location = Location::factory()
        ->for(Address::factory()->state(['latitude' => 40.0, 'longitude' => -74.0]))
        ->create();

    $result = app(CheckInDistanceCalculator::class)->resolve(40.0, -74.0);

    expect($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(CheckInDistanceDTO::class)
        ->and($result->first()->id)->toBe($location->uuid)
        ->and($result->first()->userDistance)->toBe(0.0);
});

it('sorts locations nearest first', function (): void {
    $near = Location::factory()
        ->for(Address::factory()->state(['latitude' => 40.0, 'longitude' => -74.0]))
        ->create();

    $far = Location::factory()
        ->for(Address::factory()->state(['latitude' => 34.0522, 'longitude' => -118.2437]))
        ->create();

    $result = app(CheckInDistanceCalculator::class)->resolve(40.0, -74.0);

    expect($result->pluck('id')->all())->toBe([$near->uuid, $far->uuid])
        ->and($result->first()->userDistance)->toBeLessThan($result->last()->userDistance);
});

it('caches the distance list per coordinate set', function (): void {
    Location::factory()->create();

    $calculator = app(CheckInDistanceCalculator::class);

    $first = $calculator->resolve(40.0, -74.0);

    // A location added after the first resolve must not appear while the
    // cached result for these exact coordinates is still fresh.
    Location::factory()->create();

    $second = $calculator->resolve(40.0, -74.0);

    expect($first)->toHaveCount(1)
        ->and($second)->toHaveCount(1);
});

it('recomputes the distance list for different coordinates', function (): void {
    Location::factory()->create();

    $calculator = app(CheckInDistanceCalculator::class);

    $first = $calculator->resolve(40.0, -74.0);

    Location::factory()->create();

    // Different coordinates produce a different cache key, so the new
    // location is included.
    $second = $calculator->resolve(41.0, -75.0);

    expect($first)->toHaveCount(1)
        ->and($second)->toHaveCount(2);
});

it('recomputes when the cached payload is not a valid collection of DTOs', function (): void {
    Location::factory()->create();

    $sessionId = app(UserSession::class)->getId();
    $cacheKey = sprintf('checkin_distances:%s,%s:%s', 40.0, -74.0, $sessionId);

    // Simulate a stale/incompatible cache entry (e.g. an __PHP_Incomplete_Class
    // left by pre-refactor code).
    cache()->put($cacheKey, 'corrupted-payload', now()->addMinutes(5));

    $result = app(CheckInDistanceCalculator::class)->resolve(40.0, -74.0);

    expect($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(CheckInDistanceDTO::class);
});
