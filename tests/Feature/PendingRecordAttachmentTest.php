<?php

declare(strict_types=1);

use App\Enums\RecordHistoryEvent;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

function driverWithPending(array $attributes): User
{
    return User::query()->create([
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

    $other = User::query()->create([
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

/*
 * The claim is a mass Query::update(), which fires no model events, so the
 * trail row is written by hand. This is the test that catches it going missing.
 */
it('records the claim on the record trail', function (): void {
    $checkIn = CheckIn::factory()->create(['user_id' => null]);
    $user = driverWithPending(['pending_check_in_id' => $checkIn->id]);

    event(new Verified($user));

    $row = $checkIn->history()
        ->where('event', RecordHistoryEvent::Claimed)
        ->sole();

    expect($row->subject)->toBe('email_verification')
        ->and($row->user_id)->toBe($user->id);
});

/*
 * The listener is auto-discovered from app/Listeners, so registering it
 * explicitly as well would run the claim twice and log it twice.
 */
it('records the claim exactly once', function (): void {
    $checkIn = CheckIn::factory()->create(['user_id' => null]);
    $user = driverWithPending(['pending_check_in_id' => $checkIn->id]);

    event(new Verified($user));

    expect($checkIn->history()->where('event', RecordHistoryEvent::Claimed)->count())->toBe(1);
});
