<?php

declare(strict_types=1);

use App\Enums\CheckInStatus;
use App\Models\CheckIn;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

it('renders the confirmation page for a valid check-in UUID', function (): void {
    $checkIn = CheckIn::factory()->create(['status' => CheckInStatus::Pending]);
    $checkIn->purchaseOrders()->create(['number' => 'PO-12345']);
    $location = $checkIn->location;

    get(route('checkIn.confirmed', $checkIn->uuid))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('CheckInConfirmation')
            ->has('checkIn', fn ($prop) => $prop
                ->where('uuid', $checkIn->uuid)
                ->where('referenceNumber', $checkIn->reference_number)
                ->where('customer', $checkIn->customer)
                ->where('driversName', $checkIn->drivers_name)
                ->has('destinationCity')
                ->has('destinationState')
                ->has('destinationCountry')
                ->has('locationName')
                ->has('locationAddress')
                ->where('purchaseOrders', ['PO-12345'])
            )
            ->has('contact', fn ($prop) => $prop
                ->where('phone', $location->phone)
                ->where('email', $location->email)
            )
            ->has('translations.registerCta.createAccount')
        );
});

it('returns 404 for a check-in that is not pending', function (): void {
    $checkIn = CheckIn::factory()->create(['status' => CheckInStatus::Completed]);

    get(route('checkIn.confirmed', $checkIn->uuid))
        ->assertNotFound();
});

it('returns 404 for a non-existent check-in UUID', function (): void {
    get(route('checkIn.confirmed', Str::uuid()->toString()))
        ->assertNotFound();
});
