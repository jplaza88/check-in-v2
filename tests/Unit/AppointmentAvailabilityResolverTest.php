<?php

declare(strict_types=1);

use App\Appointment\AppointmentAvailabilityResolver;
use App\Models\AppointmentScheduleOverride;
use App\Models\Location;
use Illuminate\Support\Facades\Date;

/**
 * Appointment location open 09:00-17:00 on weekdays only (no weekend hours).
 */
function appointmentWeekdayLocation(): Location
{
    $location = Location::factory()->appointmentsOnly()->create(['timezone' => 'UTC']);
    $location->appointmentSchedule()->delete();

    foreach (range(1, 5) as $dayOfWeek) {
        $location->appointmentSchedule()->create([
            'day_of_week' => $dayOfWeek,
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
        ]);
    }

    $location = $location->fresh();
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    return $location;
}

afterEach(function (): void {
    Date::setTestNow();
});

it('allows an appointment within the location schedule', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->appointmentsOnly()->create(['timezone' => 'UTC']);
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    $result = resolve(AppointmentAvailabilityResolver::class)
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

    $result = resolve(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 12:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('Maintenance');
});

it('rejects an appointment on an override closure without a reason', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 12:00:00', 'UTC')); // Monday

    $location = Location::factory()->appointmentsOnly()->create(['timezone' => 'UTC', 'name' => 'Central Yard']);
    AppointmentScheduleOverride::factory()
        ->for($location)
        ->onDate('2026-06-15')
        ->closure()
        ->create(['reason' => '']);
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    $result = resolve(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 12:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('Central Yard')
        ->and($result->reason)->toContain('not accepting appointments');
});

it('rejects an appointment outside the regular business hours', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 06:00:00', 'UTC')); // Monday, before open

    $location = appointmentWeekdayLocation();

    $result = resolve(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 06:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('Business hours');
});

it('rejects an appointment on a day with no configured hours', function (): void {
    Date::setTestNow(Date::parse('2026-06-13 12:00:00', 'UTC')); // Saturday

    $location = appointmentWeekdayLocation();

    $result = resolve(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-13 12:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('not available for appointments');
});

it('rejects an appointment outside an override special-hours window', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 08:00:00', 'UTC')); // Monday

    $location = appointmentWeekdayLocation();
    AppointmentScheduleOverride::factory()
        ->for($location)
        ->onDate('2026-06-15')
        ->specialHours('10:00:00', '14:00:00')
        ->create(['reason' => '']);
    $location->load(['appointmentSchedule', 'appointmentScheduleOverrides']);

    // 09:00 is inside the regular window but before the special-hours open time.
    $result = resolve(AppointmentAvailabilityResolver::class)
        ->isAvailableForAppointment($location, Date::parse('2026-06-15 09:00:00', 'UTC'));

    expect($result->allowed)->toBeFalse()
        ->and($result->reason)->toContain('Special hours');
});
