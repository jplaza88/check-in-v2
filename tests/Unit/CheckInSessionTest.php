<?php

declare(strict_types=1);

use App\DTOs\CheckInLocationDTO;
use App\Session\CheckInSession;

/**
 * @return array<string, bool>
 */
function checkInFormFields(): array
{
    return [
        'showTruckColor' => true,
        'showEmptyWeightLbs' => false,
        'showTruckPlateState' => false,
        'showTruckPlateCountry' => false,
        'showTrailerPlateState' => false,
        'showTrailerPlateCountry' => false,
        'showDriversLicenseState' => false,
        'showDriversLicenseCountry' => false,
        'showDriversLicenseExpirationDate' => false,
    ];
}

function checkInLocationDto(string $id = 'location-uuid'): CheckInLocationDTO
{
    return new CheckInLocationDTO(
        id: $id,
        name: 'Test Yard',
        address: '1 Main St',
        maxDistanceAllowed: 500,
        isOpen: true,
        todayOpenCloseTime: '9:00 AM - 5:00 PM EDT',
        reason: null,
        hasOverride: false,
        isOverrideClosure: false,
        isClosingSoon: false,
    );
}

it('issues a fresh gate pass for a location', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());

    expect($session->hasFreshGatePass('location-uuid'))->toBeTrue();
});

it('returns the stored location and form fields for a fresh gate pass', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());

    expect($session->getLocation())
        ->toBeInstanceOf(CheckInLocationDTO::class)
        ->and($session->getLocation()->id)->toBe('location-uuid')
        ->and($session->getCheckInFormFields())->toBe(checkInFormFields());
});

it('does not honor a gate pass issued for a different location', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());

    expect($session->hasFreshGatePass('other-location-uuid'))->toBeFalse();
});

it('returns false when no gate pass exists', function (): void {
    $session = resolve(CheckInSession::class);

    expect($session->hasFreshGatePass('location-uuid'))->toBeFalse()
        ->and($session->getLocation())->toBeNull()
        ->and($session->getCheckInFormFields())->toBeNull();
});

it('expires the gate pass after the configured ttl', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());

    $this->travel(config('app.checkin_gate_pass_ttl') + 1)->seconds();

    expect($session->hasFreshGatePass('location-uuid'))->toBeFalse()
        ->and($session->getLocation())->toBeNull();
});

it('keeps the gate pass fresh right up to the ttl boundary', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());

    $this->travel(config('app.checkin_gate_pass_ttl'))->seconds();

    expect($session->hasFreshGatePass('location-uuid'))->toBeTrue();
});

it('forgets the gate pass', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass(checkInLocationDto(), checkInFormFields());
    $session->forgetGatePass();

    expect($session->hasFreshGatePass('location-uuid'))->toBeFalse();
});
