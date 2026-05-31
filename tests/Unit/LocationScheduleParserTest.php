<?php

declare(strict_types=1);

use App\Schedule\LocationScheduleParser;
use Illuminate\Support\Facades\Date;

afterEach(function (): void {
    Date::setTestNow();
});

it('returns open when the time falls within the day schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 14:00:00', 'UTC')); // Monday

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromSchedule(
        ['open_time' => '09:00:00', 'close_time' => '17:00:00'],
        'UTC',
        Date::now('UTC'),
    );

    expect($result->isOpen)->toBeTrue()
        ->and($result->openTime)->not->toBeNull()
        ->and($result->closeTime)->not->toBeNull()
        ->and($result->reason)->toBeNull()
        ->and($result->hasOverride)->toBeFalse()
        ->and($result->isOverrideClosure)->toBeFalse();
});

it('returns closed when the time is outside the day schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 20:00:00', 'UTC')); // Monday 8pm

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromSchedule(
        ['open_time' => '09:00:00', 'close_time' => '17:00:00'],
        'UTC',
        Date::now('UTC'),
    );

    expect($result->isOpen)->toBeFalse();
});

it('returns closed with null times when no schedule exists for the day', function (): void {
    Date::setTestNow(Date::parse('2026-06-14 12:00:00', 'UTC')); // Sunday

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromSchedule(null, 'UTC', Date::now('UTC'));

    expect($result->isOpen)->toBeFalse()
        ->and($result->openTime)->toBeNull()
        ->and($result->closeTime)->toBeNull();
});

it('evaluates the schedule against the location timezone', function (): void {
    // 2026-06-15 20:00 UTC = 4:00pm America/New_York, inside a 9am-5pm ET window
    Date::setTestNow(Date::parse('2026-06-15 20:00:00', 'UTC'));

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromSchedule(
        ['open_time' => '09:00:00', 'close_time' => '17:00:00'],
        'America/New_York',
        Date::now('America/New_York'),
    );

    expect($result->isOpen)->toBeTrue();
});

it('marks an override closure as closed with its reason', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromOverride(
        [
            'open_time' => null,
            'close_time' => null,
            'is_closed' => true,
            'reason' => 'Holiday',
        ],
        'UTC',
        Date::now('UTC'),
    );

    expect($result->isOpen)->toBeFalse()
        ->and($result->reason)->toBe('Holiday')
        ->and($result->hasOverride)->toBeTrue()
        ->and($result->isOverrideClosure)->toBeTrue();
});

it('returns open for an override with modified hours within range', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 11:00:00', 'UTC')); // Monday 11am

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromOverride(
        [
            'open_time' => '10:00:00',
            'close_time' => '14:00:00',
            'is_closed' => false,
            'reason' => 'Half day',
        ],
        'UTC',
        Date::now('UTC'),
    );

    expect($result->isOpen)->toBeTrue()
        ->and($result->reason)->toBe('Half day')
        ->and($result->hasOverride)->toBeTrue()
        ->and($result->isOverrideClosure)->toBeFalse();
});

it('returns closed for an override with modified hours outside range', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 16:00:00', 'UTC')); // Monday 4pm

    $parser = new LocationScheduleParser;

    $result = $parser->resolveFromOverride(
        [
            'open_time' => '10:00:00',
            'close_time' => '14:00:00',
            'is_closed' => false,
            'reason' => 'Half day',
        ],
        'UTC',
        Date::now('UTC'),
    );

    expect($result->isOpen)->toBeFalse()
        ->and($result->hasOverride)->toBeTrue()
        ->and($result->isOverrideClosure)->toBeFalse();
});
