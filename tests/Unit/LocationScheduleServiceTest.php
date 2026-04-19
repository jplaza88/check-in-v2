<?php

declare(strict_types=1);

use App\Enums\ScheduleType;
use App\Services\LocationScheduleService;
use Illuminate\Support\Facades\Date;

afterEach(function (): void {
    Date::setTestNow();
});

it('returns open when current time falls within check-in schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 14:00:00', 'UTC')); // Monday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [],
    ];

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeTrue()
        ->and($result->openTime)->not->toBeNull()
        ->and($result->closeTime)->not->toBeNull()
        ->and($result->reason)->toBeNull()
        ->and($result->hasException)->toBeFalse()
        ->and($result->isExceptionClosure)->toBeFalse();
});

it('returns closed when current time is outside schedule hours', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 20:00:00', 'UTC')); // Monday 8pm

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'monday_open' => '09:00:00',
            'monday_close' => '17:00:00',
        ],
        'check_in_schedule_exceptions' => [],
    ];

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeFalse();
});

it('returns closed when schedule has null hours for the day', function (): void {
    Date::setTestNow(Date::parse('2026-06-14 12:00:00', 'UTC')); // Sunday

    $service = new LocationScheduleService;

    $location = [
        'timezone' => 'UTC',
        'check_in_schedule' => [
            'sunday_open' => null,
            'sunday_close' => null,
        ],
        'check_in_schedule_exceptions' => [],
    ];

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeFalse()
        ->and($result->openTime)->toBeNull()
        ->and($result->closeTime)->toBeNull();
});

it('uses exception closure over regular schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

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

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeFalse()
        ->and($result->reason)->toBe('Holiday')
        ->and($result->hasException)->toBeTrue()
        ->and($result->isExceptionClosure)->toBeTrue();
});

it('uses exception with modified hours over regular schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 11:00:00', 'UTC')); // Monday 11am

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

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeTrue()
        ->and($result->reason)->toBe('Half day')
        ->and($result->hasException)->toBeTrue()
        ->and($result->isExceptionClosure)->toBeFalse();
});

it('ignores exceptions for other dates', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

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

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeTrue()
        ->and($result->hasException)->toBeFalse();
});

it('resolves appointment schedule separately from check-in schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

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

    $resultCheckIn = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);
    $resultAppointment = $service->resolveOpenCloseTime($location, ScheduleType::Appointment);

    expect($resultCheckIn->isOpen)->toBeTrue()
        ->and($resultAppointment->isOpen)->toBeFalse();
});

it('respects timezone when resolving schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-16 01:00:00', 'UTC')); // Monday 1am UTC = Sunday 9pm ET

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

    $result = $service->resolveOpenCloseTime($location, ScheduleType::CheckIn);

    expect($result->isOpen)->toBeFalse();
});
