<?php

declare(strict_types=1);

use App\Appointment\AppointmentAvailabilityResolver;
use App\Models\AppointmentScheduleOverride;
use App\Models\Location;
use Illuminate\Support\Facades\Date;

afterEach(function (): void {
    Date::setTestNow();
});

it('allows an appointment within the location schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->appointmentsOnly()->create(['timezone' => 'UTC']);
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    $result = app(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 12:00:00', 'UTC'));

    expect($result->allowed)->toBeTrue()
        ->and($result->reason)->toBeNull();
});

it('rejects an appointment on an override closure with a reason', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->appointmentsOnly()->create(['timezone' => 'UTC']);
    AppointmentScheduleOverride::factory()
        ->for($location)
        ->onDate('2026-06-15')
        ->closure()
        ->create(['reason' => 'Maintenance']);
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    $result = app(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 12:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('Maintenance');
});
