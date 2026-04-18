<?php

declare(strict_types=1);

use App\Services\AddressService;

it('builds a full address from all parts', function (): void {
    $service = new AddressService;

    $address = $service->buildAddress([
        'street1' => '123 Main St',
        'street2' => 'Suite 100',
        'city' => 'Miami',
        'state' => 'FL',
        'zip_code' => '33101',
    ]);

    expect($address)->toBe('123 Main St, Suite 100, Miami, FL 33101');
});

it('omits street2 when null', function (): void {
    $service = new AddressService;

    $address = $service->buildAddress([
        'street1' => '456 Oak Ave',
        'street2' => null,
        'city' => 'Orlando',
        'state' => 'FL',
        'zip_code' => '32801',
    ]);

    expect($address)->toBe('456 Oak Ave, Orlando, FL 32801');
});

it('omits street2 when missing from array', function (): void {
    $service = new AddressService;

    $address = $service->buildAddress([
        'street1' => '789 Pine Rd',
        'city' => 'Tampa',
        'state' => 'FL',
        'zip_code' => '33602',
    ]);

    expect($address)->toBe('789 Pine Rd, Tampa, FL 33602');
});
