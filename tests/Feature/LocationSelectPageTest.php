<?php

declare(strict_types=1);

use App\Models\Location;
use Inertia\Testing\AssertableInertia;

// Drivers use route context `checkin` for load pickup at DCs. `appointment` is a separate scheduled-visit flow;
// locations can enable check-in, appointments, or both — the second test below covers the appointment-only case.

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
