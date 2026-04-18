<?php

declare(strict_types=1);

use App\Services\CheckInAvailabilityService;
use App\Services\LocationScheduleService;

it('calculates zero distance for identical coordinates', function (): void {
    $service = new CheckInAvailabilityService(new LocationScheduleService);

    expect($service->distance(40.7128, -74.006, 40.7128, -74.006))->toBe(0.0);
});

it('calculates distance between two known points in miles', function (): void {
    $service = new CheckInAvailabilityService(new LocationScheduleService);

    $distance = $service->distance(40.7128, -74.006, 34.0522, -118.2437);

    expect($distance)->toBeGreaterThan(2400)
        ->and($distance)->toBeLessThan(2500);
});

it('respects decimal places parameter', function (): void {
    $service = new CheckInAvailabilityService(new LocationScheduleService);

    $twoDecimals = $service->distance(40.7128, -74.006, 40.7580, -73.9855, 2);
    $zeroDecimals = $service->distance(40.7128, -74.006, 40.7580, -73.9855, 0);

    expect($twoDecimals)->toBe(round($twoDecimals, 2))
        ->and($zeroDecimals)->toBe(round($zeroDecimals, 0));
});

it('returns symmetric distance regardless of direction', function (): void {
    $service = new CheckInAvailabilityService(new LocationScheduleService);

    $forward = $service->distance(40.7128, -74.006, 26.142, -80.478);
    $reverse = $service->distance(26.142, -80.478, 40.7128, -74.006);

    expect($forward)->toBe($reverse);
});
