<?php

declare(strict_types=1);

use App\Models\Location;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

// Drivers use route context `checkin` for load pickup at DCs. `appointment` is a separate scheduled-visit flow;
// locations can enable check-in, appointments, or both - the second test below covers the appointment-only case.

it('renders the check-in location select page with check-in-enabled locations', function (): void {
    $checkInLocation = Location::factory()->create(['name' => 'Everglades Watermelons']);
    Location::factory()->appointmentsOnly()->create(['name' => 'Appointments Only Warehouse']);

    $this->get(route('checkIn.selectLocation'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('CheckInSelectLocation')
            ->has('locations', 1)
            ->where('locations.0.name', $checkInLocation->name));
});

it('renders the appointment location select page with appointment-enabled locations', function (): void {
    Location::factory()->create(['name' => 'Everglades Watermelons']);
    $appointmentLocation = Location::factory()->appointmentsOnly()->create(['name' => 'Appointments Only Warehouse']);

    $this->get(route('appointment.selectLocation'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('AppointmentSelectLocation')
            ->has('locations', 1)
            ->where('locations.0.name', $appointmentLocation->name));
});

it('leaves the appointment ordering alone for guests', function (): void {
    $first = Location::factory()->appointmentsOnly()->create(['name' => 'Alpha Yard']);
    $second = Location::factory()->appointmentsOnly()->create(['name' => 'Beta Yard']);

    $this->get(route('appointment.selectLocation'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('usualLocationId', null)
            ->where('locations.0.name', $first->name)
            ->where('locations.1.name', $second->name));
});

/*
 * The appointment picker has no coordinates to sort by, so the stored preference is the only ordering
 * signal available. Drivers book from home days ahead, which is why check-in's distance sort cannot help.
 */
it('floats the usual location to the top of the appointment picker', function (): void {
    Location::factory()->appointmentsOnly()->create(['name' => 'Alpha Yard']);
    $usual = Location::factory()->appointmentsOnly()->create(['name' => 'Beta Yard']);
    Location::factory()->appointmentsOnly()->create(['name' => 'Gamma Yard']);

    $driver = usualLocationDriver($usual);

    $this->actingAs($driver)
        ->get(route('appointment.selectLocation'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('usualLocationId', $usual->uuid)
            ->where('locations.0.name', $usual->name)
            // Everything else keeps the resolver's ordering.
            ->where('locations.1.name', 'Alpha Yard')
            ->where('locations.2.name', 'Gamma Yard'));
});

it('ignores a usual location that is no longer bookable', function (): void {
    Location::factory()->appointmentsOnly()->create(['name' => 'Alpha Yard']);
    $retired = Location::factory()->appointmentsOnly()->create(['name' => 'Beta Yard']);

    $driver = usualLocationDriver($retired);
    $retired->update(['is_appointments_enabled' => false]);

    $this->actingAs($driver)
        ->get(route('appointment.selectLocation'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('usualLocationId', null)
            ->has('locations', 1)
            ->where('locations.0.name', 'Alpha Yard'));
});

it('ignores a usual location that has been soft deleted', function (): void {
    Location::factory()->appointmentsOnly()->create(['name' => 'Alpha Yard']);
    $deleted = Location::factory()->appointmentsOnly()->create(['name' => 'Beta Yard']);

    $driver = usualLocationDriver($deleted);
    $deleted->delete();

    $this->actingAs($driver)
        ->get(route('appointment.selectLocation'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('usualLocationId', null)
            ->has('locations', 1));
});

function usualLocationDriver(Location $location): User
{
    $driver = User::query()->create([
        'name' => 'John Driver',
        'email' => 'driver@example.com',
        'password' => 'secret-password',
    ]);

    $driver->forceFill(['location_id' => $location->id])->save();

    return $driver;
}
