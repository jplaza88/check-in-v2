<?php

declare(strict_types=1);

use App\CheckIn\CheckInScheduleResolver;
use App\Models\Location;

/**
 * @return array{checkInGatePass: array{uuid: string, location: App\DTOs\CheckInLocationDTO, fields: array<string, bool>, storedAt: int}}
 */
function freshGatePass(Location $location): array
{
    return [
        'checkInGatePass' => [
            'uuid' => $location->uuid,
            'location' => resolve(CheckInScheduleResolver::class)->buildDTO($location, now()),
            'fields' => $location->checkInFormFields(),
            'storedAt' => now()->timestamp,
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function validCheckInPayload(): array
{
    return [
        'customer' => 'Acme Produce',
        'destination_city' => 'Phoenix',
        'destination_state' => 'Arizona',
        'loading_instructions' => 'Dock 4, call on arrival.',
        'po_numbers' => ['PO-12345'],
        'truck_name' => 'Truck 42',
        'truck_plate' => 'ABC1234',
        'trailer_plate' => 'XYZ9876',
        'trailer_chute' => '4',
        'drivers_name' => 'John Driver',
        'drivers_cellphone' => '(555) 123-4567',
        'drivers_license_number' => 'D12345678',
    ];
}

it('redirects to location select when the gate pass is missing', function (): void {
    $location = Location::factory()->create();

    $this->get(route('checkIn.form', $location))
        ->assertRedirect(route('checkIn.selectLocation'))
        ->assertSessionHasErrors('uuid');
});

it('renders the check-in form with the location and field flags', function (): void {
    $location = Location::factory()->checkinAllOptionalFields()->create();

    $response = $this->withSession(freshGatePass($location))
        ->get(route('checkIn.form', $location));

    $response->assertSuccessful();

    $page = $response->viewData('page');

    expect($page['component'])->toBe('CheckInForm')
        ->and($page['props']['location']->id)->toBe($location->uuid)
        ->and($page['props']['fields'])->toBe([
            'showTruckColor' => true,
            'showEmptyWeightLbs' => true,
            'showTruckPlateState' => true,
            'showTruckPlateCountry' => true,
            'showTrailerPlateState' => true,
            'showTrailerPlateCountry' => true,
            'showDriversLicenseState' => true,
            'showDriversLicenseCountry' => true,
            'showDriversLicenseExpirationDate' => true,
        ]);
});

it('hides optional fields when the location config does not enable them', function (): void {
    $location = Location::factory()->create();

    $response = $this->withSession(freshGatePass($location))
        ->get(route('checkIn.form', $location));

    expect($response->viewData('page')['props']['fields'])->toBe([
        'showTruckColor' => false,
        'showEmptyWeightLbs' => false,
        'showTruckPlateState' => false,
        'showTruckPlateCountry' => false,
        'showTrailerPlateState' => false,
        'showTrailerPlateCountry' => false,
        'showDriversLicenseState' => false,
        'showDriversLicenseCountry' => false,
        'showDriversLicenseExpirationDate' => false,
    ]);
});

it('validates the required check-in fields', function (): void {
    $location = Location::factory()->create();

    $this->withSession(freshGatePass($location))
        ->from(route('checkIn.form', $location))
        ->post(route('checkIn.store', $location), [])
        ->assertRedirect(route('checkIn.form', $location))
        ->assertSessionHasErrors([
            'customer',
            'destination_city',
            'destination_state',
            'po_numbers',
            'truck_name',
            'truck_plate',
            'trailer_plate',
            'trailer_chute',
            'drivers_name',
            'drivers_cellphone',
            'drivers_license_number',
        ]);
});

it('accepts a valid submission when optional fields are hidden', function (): void {
    $location = Location::factory()->create();

    $this->withSession(freshGatePass($location))
        ->from(route('checkIn.form', $location))
        ->post(route('checkIn.store', $location), validCheckInPayload())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('checkIn.selectLocation'));
});

it('requires the optional fields when the location config shows them', function (): void {
    $location = Location::factory()->checkinAllOptionalFields()->create();

    $this->withSession(freshGatePass($location))
        ->from(route('checkIn.form', $location))
        ->post(route('checkIn.store', $location), validCheckInPayload())
        ->assertRedirect(route('checkIn.form', $location))
        ->assertSessionHasErrors([
            'truck_color',
            'truck_plate_state',
            'truck_plate_country',
            'trailer_plate_state',
            'trailer_plate_country',
            'empty_weight_lbs',
            'drivers_license_state',
            'drivers_license_country',
            'drivers_license_expiration_date',
        ]);
});

it('accepts a valid submission including the config-driven fields', function (): void {
    $location = Location::factory()->checkinAllOptionalFields()->create();

    $payload = [
        ...validCheckInPayload(),
        'truck_color' => 'White',
        'truck_plate_state' => 'Arizona',
        'truck_plate_country' => 'us',
        'trailer_plate_state' => 'Sonora',
        'trailer_plate_country' => 'mx',
        'empty_weight_lbs' => 15000,
        'drivers_license_state' => 'Arizona',
        'drivers_license_country' => 'US',
        'drivers_license_expiration_date' => now()->addYear()->format('Y-m-d'),
    ];

    $this->withSession(freshGatePass($location))
        ->from(route('checkIn.form', $location))
        ->post(route('checkIn.store', $location), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('checkIn.selectLocation'));
});

it('rejects an expired drivers license', function (): void {
    $location = Location::factory()->checkinAllOptionalFields()->create();

    $payload = [
        ...validCheckInPayload(),
        'truck_color' => 'White',
        'truck_plate_state' => 'Arizona',
        'truck_plate_country' => 'US',
        'trailer_plate_state' => 'Sonora',
        'trailer_plate_country' => 'MX',
        'empty_weight_lbs' => 15000,
        'drivers_license_state' => 'Arizona',
        'drivers_license_country' => 'US',
        'drivers_license_expiration_date' => now()->subDay()->format('Y-m-d'),
    ];

    $this->withSession(freshGatePass($location))
        ->from(route('checkIn.form', $location))
        ->post(route('checkIn.store', $location), $payload)
        ->assertSessionHasErrors([
            'drivers_license_expiration_date' => __('messages.checkInForm.licenseExpired'),
        ]);
});

it('rejects the submission when the gate pass has expired', function (): void {
    $location = Location::factory()->create();

    $this->withSession([
        'checkInGatePass' => [
            'uuid' => $location->uuid,
            'storedAt' => now()->subSeconds((int) config('app.checkin_gate_pass_ttl') + 1)->timestamp,
        ],
    ])
        ->post(route('checkIn.store', $location), validCheckInPayload())
        ->assertRedirect(route('checkIn.selectLocation'))
        ->assertSessionHasErrors('uuid');
});
