<?php

declare(strict_types=1);

use App\Session\CheckInSession;

it('issues a fresh gate pass for a location', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass('location-uuid');

    expect($session->hasFreshGatePass('location-uuid'))->toBeTrue();
});

it('does not honor a gate pass issued for a different location', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass('location-uuid');

    expect($session->hasFreshGatePass('other-location-uuid'))->toBeFalse();
});

it('returns false when no gate pass exists', function (): void {
    expect(resolve(CheckInSession::class)->hasFreshGatePass('location-uuid'))->toBeFalse();
});

it('expires the gate pass after the configured ttl', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass('location-uuid');

    $this->travel(config('app.checkin_gate_pass_ttl') + 1)->seconds();

    expect($session->hasFreshGatePass('location-uuid'))->toBeFalse();
});

it('keeps the gate pass fresh right up to the ttl boundary', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass('location-uuid');

    $this->travel(config('app.checkin_gate_pass_ttl'))->seconds();

    expect($session->hasFreshGatePass('location-uuid'))->toBeTrue();
});

it('forgets the gate pass', function (): void {
    $session = resolve(CheckInSession::class);

    $session->issueGatePass('location-uuid');
    $session->forgetGatePass();

    expect($session->hasFreshGatePass('location-uuid'))->toBeFalse();
});
