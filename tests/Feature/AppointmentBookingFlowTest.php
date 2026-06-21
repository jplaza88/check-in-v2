<?php

declare(strict_types=1);

use App\Models\Location;
use Illuminate\Support\Str;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('stores the location context after clearing the appointment gate', function (): void {
    $location = Location::factory()->appointmentsOnly()->create();

    post(route('appointment.gate', $location->uuid), ['uuid' => $location->uuid])
        ->assertRedirect(route('appointment.form', $location->uuid));
});

it('renders the booking form once the gate context exists', function (): void {
    $location = Location::factory()->appointmentsOnly()->create();

    post(route('appointment.gate', $location->uuid), ['uuid' => $location->uuid]);

    get(route('appointment.form', $location->uuid))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('AppointmentForm'));
});

it('exposes availability windows and the slot interval in the booking form props', function (): void {
    $location = Location::factory()->appointmentsOnly()->create();

    post(route('appointment.gate', $location->uuid), ['uuid' => $location->uuid]);

    get(route('appointment.form', $location->uuid))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('AppointmentForm')
            ->has('location.availability')
            ->has('location.slotIntervalMinutes')
        );
});

it('bootstraps the appointment context from a deep link to a valid location', function (): void {
    $location = Location::factory()->appointmentsOnly()->create();

    get(route('appointment.form', $location->uuid))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('AppointmentForm'));
});

it('redirects to select location when the appointment location does not exist', function (): void {
    get(route('appointment.form', Str::uuid()->toString()))
        ->assertRedirect(route('appointment.selectLocation'));
});

it('redirects to select location when the location is not appointment enabled', function (): void {
    $location = Location::factory()->create(['is_appointments_enabled' => false]);

    get(route('appointment.form', $location->uuid))
        ->assertRedirect(route('appointment.selectLocation'));
});
