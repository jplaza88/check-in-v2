<?php

declare(strict_types=1);

use App\Enums\ScheduleType;
use App\Services\LocationScheduleService;
use Carbon\Carbon;

afterEach(function () {
    Carbon::setTestNow();
});

it('returns open when current time falls within check-in schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 14:00:00', 'UTC')); // Monday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [],
    ];

    [$isOpen, $openTime, $closeTime, $reason, $hasException, $isExceptionClosure] =
        $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeTrue()
        ->and($openTime)->not->toBeNull()
        ->and($closeTime)->not->toBeNull()
        ->and($reason)->toBeNull()
        ->and($hasException)->toBeFalse()
        ->and($isExceptionClosure)->toBeFalse();
});

it('returns closed when current time is outside schedule hours', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 20:00:00', 'UTC')); // Monday 8pm

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [],
    ];

    [$isOpen] = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeFalse();
});

it('returns closed when schedule has null hours for the day', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-14 12:00:00', 'UTC')); // Sunday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'sunday_open' => null,
            'sunday_close' => null,
        ],
        'check_in_schedule_exceptions' => [],
    ];

    [$isOpen, $openTime, $closeTime] = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeFalse()
        ->and($openTime)->toBeNull()
        ->and($closeTime)->toBeNull();
});

it('uses exception closure over regular schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [
            [
                'date' => '2026-06-15',
                'open' => null,
                'close' => null,
                'is_closed' => true,
                'reason' => 'Holiday',
            ],
        ],
    ];

    [$isOpen, $openTime, $closeTime, $reason, $hasException, $isExceptionClosure] =
        $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeFalse()
        ->and($reason)->toBe('Holiday')
        ->and($hasException)->toBeTrue()
        ->and($isExceptionClosure)->toBeTrue();
});

it('uses exception with modified hours over regular schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 11:00:00', 'UTC')); // Monday 11am

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [
            [
                'date' => '2026-06-15',
                'open' => '10:00:00',
                'close' => '14:00:00',
                'is_closed' => false,
                'reason' => 'Half day',
            ],
        ],
    ];

    [$isOpen, $openTime, $closeTime, $reason, $hasException, $isExceptionClosure] =
        $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeTrue()
        ->and($reason)->toBe('Half day')
        ->and($hasException)->toBeTrue()
        ->and($isExceptionClosure)->toBeFalse();
});

it('ignores exceptions for other dates', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [
            [
                'date' => '2026-06-16',
                'open' => null,
                'close' => null,
                'is_closed' => true,
                'reason' => 'Tomorrow closure',
            ],
        ],
    ];

    [$isOpen, $reason, $hasException] = (function () use ($service, $location) {
        [$isOpen, , , $reason, $hasException] = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

        return [$isOpen, $reason, $hasException];
    })();

    expect($isOpen)->toBeTrue()
        ->and($hasException)->toBeFalse();
});

it('resolves appointment schedule separately from check-in schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '00:00:00',
            'monday_close' => '23:59:59',
        ],
        'check_in_schedule_exceptions' => [],
        'appointment_schedule' => [
            'monday_open' => null,
            'monday_close' => null,
        ],
        'appointment_schedule_exceptions' => [],
    ];

    [$checkInOpen] = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);
    [$appointmentOpen] = $service->resolveOpenCloseTime($location, ScheduleType::Appointment);

    expect($checkInOpen)->toBeTrue()
        ->and($appointmentOpen)->toBeFalse();
});

it('respects timezone when resolving schedule', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-16 01:00:00', 'UTC')); // Monday 1am UTC = Sunday 9pm ET

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'America/New_York',
        'check_in_schedule' => [
            'sunday_open' => null,
            'sunday_close' => null,
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [],
    ];

    [$isOpen] = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($isOpen)->toBeFalse();
});
