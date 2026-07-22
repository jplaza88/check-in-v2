<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Enums\CheckInStatus;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;

it('redirects guests to the login page', function (): void {
    get(route('account'))->assertRedirect(route('login'));
});

it('renders the driver account for an authenticated user', function (): void {
    $user = User::create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);

    $this->actingAs($user)
        ->get(route('account'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Account')
            ->where('nextAppointment', null)
            ->where('recentCheckIns', [])
            ->has('translations.account.checkInNow'));
});

it('reports whether the driver has a license on file for the hub chips and nudges', function (): void {
    $without = User::create([
        'name' => 'No License',
        'email' => 'nolicense@example.com',
        'password' => 'secret-password',
    ]);

    $this->actingAs($without)
        ->get(route('account'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('hasLicense', false)
            ->has('translations.accountNav.overview'));

    $with = User::create([
        'name' => 'Has License',
        'email' => 'haslicense@example.com',
        'password' => 'secret-password',
        'drivers_license_number' => 'D1234567',
    ]);

    $this->actingAs($with)
        ->get(route('account'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->where('hasLicense', true));
});

it('surfaces the driver\'s next appointment and recent check-ins', function (): void {
    $user = User::create([
        'name' => 'John Driver',
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);
    $location = Location::factory()->create();

    $appointment = Appointment::factory()->create([
        'location_id' => $location->id,
        'user_id' => $user->id,
        'status' => AppointmentStatus::Scheduled,
        'scheduled_for' => now()->addDays(2),
    ]);

    CheckIn::factory()->create([
        'location_id' => $location->id,
        'user_id' => $user->id,
        'status' => CheckInStatus::Pending,
    ]);

    $this->actingAs($user)
        ->get(route('account'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Account')
            ->has('nextAppointment', fn (AssertableInertia $prop): AssertableInertia => $prop
                ->where('referenceNumber', $appointment->reference_number)
                ->where('locationName', $location->name)
                ->has('monthShort')
                ->has('day')
                ->has('time'))
            ->has('recentCheckIns', 1));
});
