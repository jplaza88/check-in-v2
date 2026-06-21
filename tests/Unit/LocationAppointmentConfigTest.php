<?php

declare(strict_types=1);

use App\Models\Location;

/**
 * @param  array<string, int>  $appointment
 */
function locationWithAppointmentConfig(array $appointment): Location
{
    return Location::factory()->create([
        'config' => ['appointment' => $appointment],
    ]);
}

it('falls back to the app defaults when no config is present', function (): void {
    $location = Location::factory()->create(['config' => null]);

    expect($location->appointmentMaxBookingDaysAhead())->toBe(7)
        ->and($location->appointmentSlotIntervalMinutes())->toBe(15)
        ->and($location->appointmentMinLeadTimeMinutes())->toBe(60)
        ->and($location->appointmentBufferBeforeCloseMinutes())->toBe(30);
});

it('honors valid per-location overrides', function (): void {
    $location = locationWithAppointmentConfig([
        'max_booking_days_ahead' => 5,
        'slot_interval_minutes' => 30,
        'min_lead_time_minutes' => 120,
        'buffer_before_close_minutes' => 45,
    ]);

    expect($location->appointmentMaxBookingDaysAhead())->toBe(5)
        ->and($location->appointmentSlotIntervalMinutes())->toBe(30)
        ->and($location->appointmentMinLeadTimeMinutes())->toBe(120)
        ->and($location->appointmentBufferBeforeCloseMinutes())->toBe(45);
});

it('clamps the booking horizon to the safeguard cap', function (): void {
    expect(locationWithAppointmentConfig(['max_booking_days_ahead' => 999])->appointmentMaxBookingDaysAhead())->toBe(7)
        ->and(locationWithAppointmentConfig(['max_booking_days_ahead' => 0])->appointmentMaxBookingDaysAhead())->toBe(1);
});

it('falls back to the default slot interval when the value is not allowlisted', function (): void {
    expect(locationWithAppointmentConfig(['slot_interval_minutes' => 7])->appointmentSlotIntervalMinutes())->toBe(15)
        ->and(locationWithAppointmentConfig(['slot_interval_minutes' => 60])->appointmentSlotIntervalMinutes())->toBe(60);
});

it('clamps lead time and buffer to their safeguard maxes', function (): void {
    $location = locationWithAppointmentConfig([
        'min_lead_time_minutes' => 9999,
        'buffer_before_close_minutes' => 9999,
    ]);

    expect($location->appointmentMinLeadTimeMinutes())->toBe(480)
        ->and($location->appointmentBufferBeforeCloseMinutes())->toBe(480);
});
