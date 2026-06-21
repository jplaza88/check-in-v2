<?php

declare(strict_types=1);

use App\DTOs\AppointmentAvailabilityWindowDTO;
use App\DTOs\AppointmentLocationDTO;
use App\Session\AppointmentSession;

function appointmentLocationDto(string $id = 'location-uuid'): AppointmentLocationDTO
{
    return new AppointmentLocationDTO(
        id: $id,
        name: 'Test Yard',
        address: '1 Main St',
        isOpen: true,
        todayOpenCloseTime: '09:00 - 17:00',
        reason: null,
        hasOverride: false,
        isOverrideClosure: false,
        isClosingSoon: false,
        availability: [
            new AppointmentAvailabilityWindowDTO(
                date: '2026-06-15',
                firstSlot: '09:00',
                lastSlot: '16:30',
            ),
        ],
        slotIntervalMinutes: 30,
    );
}

it('stores and returns the selected appointment location', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());

    expect($session->getLocation())
        ->toBeInstanceOf(AppointmentLocationDTO::class)
        ->and($session->getLocation()->id)->toBe('location-uuid');
});

it('considers the context fresh for the matching location', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());

    expect($session->hasFreshLocation('location-uuid'))->toBeTrue();
});

it('does not honor the context for a different location', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());

    expect($session->hasFreshLocation('other-location-uuid'))->toBeFalse();
});

it('returns null when no context exists', function (): void {
    expect(resolve(AppointmentSession::class)->getLocation())->toBeNull();
});

it('keeps the context fresh right up to the ttl boundary', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());

    $this->travel(config('app.user_appointment_location_context_ttl'))->seconds();

    expect($session->getLocation())->toBeInstanceOf(AppointmentLocationDTO::class)
        ->and($session->hasFreshLocation('location-uuid'))->toBeTrue();
});

it('expires the context after the configured ttl', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());

    $this->travel(config('app.user_appointment_location_context_ttl') + 1)->seconds();

    expect($session->getLocation())->toBeNull()
        ->and($session->hasFreshLocation('location-uuid'))->toBeFalse();
});

it('forgets the context', function (): void {
    $session = resolve(AppointmentSession::class);

    $session->setLocation(appointmentLocationDto());
    $session->forgetLocation();

    expect($session->getLocation())->toBeNull();
});
