<?php

declare(strict_types=1);

use App\Enums\ScheduleType;

it('has correct backing values', function (): void {
    expect(ScheduleType::CheckIn->value)->toBe('checkin')
        ->and(ScheduleType::Appointment->value)->toBe('appointment');
});

it('resolves from valid string values', function (): void {
    expect(ScheduleType::from('checkin'))->toBe(ScheduleType::CheckIn)
        ->and(ScheduleType::from('appointment'))->toBe(ScheduleType::Appointment);
});

it('throws on invalid string value', function (): void {
    ScheduleType::from('invalid');
})->throws(ValueError::class);

it('returns correct labels', function (): void {
    expect(ScheduleType::CheckIn->label())->toBe('Check-In')
        ->and(ScheduleType::Appointment->label())->toBe('Appointment');
});

it('returns correct select location pages', function (): void {
    expect(ScheduleType::CheckIn->selectLocationPage())->toBe('CheckInSelectLocation')
        ->and(ScheduleType::Appointment->selectLocationPage())->toBe('AppointmentSelectLocation');
});

it('returns correct location enabled columns', function (): void {
    expect(ScheduleType::CheckIn->locationEnabledColumn())->toBe('is_checkins_enabled')
        ->and(ScheduleType::Appointment->locationEnabledColumn())->toBe('is_appointments_enabled');
});

it('returns camelCase schedule relationships by default', function (): void {
    expect(ScheduleType::CheckIn->scheduleRelationships())
        ->toBe(['checkInSchedule', 'checkInScheduleExceptions'])
        ->and(ScheduleType::Appointment->scheduleRelationships())
        ->toBe(['appointmentSchedule', 'appointmentScheduleExceptions']);
});

it('returns snake_case schedule relationships when flag is set', function (): void {
    expect(ScheduleType::CheckIn->scheduleRelationships(snake: true))
        ->toBe(['check_in_schedule', 'check_in_schedule_exceptions'])
        ->and(ScheduleType::Appointment->scheduleRelationships(snake: true))
        ->toBe(['appointment_schedule', 'appointment_schedule_exceptions']);
});
