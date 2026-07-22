<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

function driverWithPending(array $attributes): User
{
    return User::create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'secret-password',
        ...$attributes,
    ]);
}

it('attaches and stamps a pending check-in once the email is verified', function (): void {
    $location = Location::factory()->create();
    $checkIn = CheckIn::factory()->create([
        'location_id' => $location->id,
        'user_id' => null,
    ]);

    $user = driverWithPending(['pending_check_in_id' => $checkIn->id]);

    event(new Verified($user));

    $checkIn->refresh();

    expect($checkIn->user_id)->toBe($user->id)
        ->and($checkIn->claimed_via)->toBe('email_verification')
        ->and($checkIn->claimed_at)->not->toBeNull()
        ->and($user->refresh()->pending_check_in_id)->toBeNull();
});

it('attaches and stamps a pending appointment once the email is verified', function (): void {
    $location = Location::factory()->create();
    $appointment = Appointment::factory()->create([
        'location_id' => $location->id,
        'user_id' => null,
    ]);

    $user = driverWithPending(['pending_appointment_id' => $appointment->id]);

    event(new Verified($user));

    $appointment->refresh();

    expect($appointment->user_id)->toBe($user->id)
        ->and($appointment->claimed_via)->toBe('email_verification')
        ->and($appointment->claimed_at)->not->toBeNull()
        ->and($user->refresh()->pending_appointment_id)->toBeNull();
});

it('does not claim a check-in that already has a user', function (): void {
    $location = Location::factory()->create();
    $owner = driverWithPending([]);
    $checkIn = CheckIn::factory()->create([
        'location_id' => $location->id,
        'user_id' => $owner->id,
    ]);

    $other = User::create([
        'name' => 'Other Driver',
        'email' => 'other@example.com',
        'password' => 'secret-password',
        'pending_check_in_id' => $checkIn->id,
    ]);

    event(new Verified($other));

    $checkIn->refresh();

    expect($checkIn->user_id)->toBe($owner->id)
        ->and($checkIn->claimed_at)->toBeNull()
        ->and($other->refresh()->pending_check_in_id)->toBeNull();
});
