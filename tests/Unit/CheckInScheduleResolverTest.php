<?php

declare(strict_types=1);

use App\CheckIn\CheckInScheduleResolver;
use App\Models\CheckInScheduleOverride;
use App\Models\Location;
use Illuminate\Support\Facades\Date;

afterEach(function (): void {
    Date::setTestNow();
});

it('resolves an open schedule for a location open all week', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->create(['timezone' => 'UTC']);
    $location->load(['address', 'checkinSchedule', 'checkinScheduleOverrides']);

    $schedule = app(CheckInScheduleResolver::class)->resolveSchedule($location, Date::now('UTC'));

    expect($schedule->isOpen)->toBeTrue()
        ->and($schedule->hasOverride)->toBeFalse();
});

it('resolves open on a weekday within the pickup window', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday noon

    $location = Location::factory()->checkinWeekdayWindow()->create(['timezone' => 'UTC']);
    $location->load(['address', 'checkinSchedule', 'checkinScheduleOverrides']);

    $schedule = app(CheckInScheduleResolver::class)->resolveSchedule($location, Date::now('UTC'));

    expect($schedule->isOpen)->toBeTrue();
});

it('resolves closed on a weekend with no pickup window', function (): void {
    Date::setTestNow(Date::parse('2026-06-13 12:00:00', 'UTC')); // Saturday

    $location = Location::factory()->checkinWeekdayWindow()->create(['timezone' => 'UTC']);
    $location->load(['address', 'checkinSchedule', 'checkinScheduleOverrides']);

    $schedule = app(CheckInScheduleResolver::class)->resolveSchedule($location, Date::now('UTC'));

    expect($schedule->isOpen)->toBeFalse()
        ->and($schedule->openTime)->toBeNull()
        ->and($schedule->closeTime)->toBeNull();
});

it('prefers an override closure over the regular schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->create(['timezone' => 'UTC']);
    CheckInScheduleOverride::factory()
        ->for($location)
        ->onDate('2026-06-15')
        ->closure()
        ->create(['reason' => 'Holiday']);
    $location->load(['address', 'checkinSchedule', 'checkinScheduleOverrides']);

    $schedule = app(CheckInScheduleResolver::class)->resolveSchedule($location, Date::now('UTC'));

    expect($schedule->isOpen)->toBeFalse()
        ->and($schedule->hasOverride)->toBeTrue()
        ->and($schedule->isOverrideClosure)->toBeTrue()
        ->and($schedule->reason)->toBe('Holiday');
});
