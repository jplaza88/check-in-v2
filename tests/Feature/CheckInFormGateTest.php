<?php

declare(strict_types=1);

use App\Models\Address;
use App\Models\CheckInSchedule;
use App\Models\Location;
use Illuminate\Support\Facades\Date;

afterEach(function (): void {
    Date::setTestNow();
});

it('redirects to location select when session coordinates are missing', function (): void {
    $location = Location::factory()->create();

    $this->post(route('checkIn.form', $location))
        ->assertRedirect(route('checkIn.selectLocation'));
});

it('passes validation and renders the check-in form after a successful gate post', function (): void {
    $location = Location::factory()
        ->for(Address::factory()->state([
            'latitude' => 40.7128,
            'longitude' => -74.006,
        ]))
        ->create();

    $response = $this->withSession([
        'userCoords' => [
            'latitude' => 40.7128,
            'longitude' => -74.006,
            'storedAt' => now()->timestamp,
        ],
    ])->post(route('checkIn.form', $location));

    $response->assertSuccessful();

    $page = $response->viewData('page');

    expect($page['component'])->toBe('CheckInForm')
        ->and($page['props']['location']['uuid'])->toBe($location->uuid);
});

it('rejects check-in when the distribution center has no operating hours', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 14:00:00', 'UTC'));

    $location = Location::factory()
        ->for(Address::factory()->state([
            'latitude' => 40.7128,
            'longitude' => -74.006,
        ]))
        ->create([
            'timezone' => 'America/New_York',
        ]);

    $location->checkInSchedule->update(
        CheckInSchedule::factory()->closedEveryDay()->make()->getAttributes()
    );

    $this->from(route('checkIn.selectLocation'))
        ->withSession([
            'userCoords' => [
                'latitude' => 40.7128,
                'longitude' => -74.006,
                'storedAt' => now()->timestamp,
            ],
        ])
        ->post(route('checkIn.form', $location))
        ->assertRedirect()
        ->assertSessionHasErrors([
            'uuid' => __('messages.checkInSelectLocation.closedForCheckInToday', ['name' => $location->name]),
        ]);
});

it('rejects check-in when the driver arrives outside the weekday pickup window', function (): void {
    Date::setTestNow(Date::parse('2026-06-15 20:00:00', 'America/New_York'));

    $location = Location::factory()
        ->for(Address::factory()->state([
            'latitude' => 26.142,
            'longitude' => -80.478,
        ]))
        ->create([
            'timezone' => 'America/New_York',
        ]);

    $location->checkInSchedule->update(
        CheckInSchedule::factory()->weekdayPickupWindow()->make()->getAttributes()
    );

    $this->from(route('checkIn.selectLocation'))
        ->withSession([
            'userCoords' => [
                'latitude' => 26.142,
                'longitude' => -80.478,
                'storedAt' => now()->timestamp,
            ],
        ])
        ->post(route('checkIn.form', $location))
        ->assertRedirect()
        ->assertSessionHasErrors([
            'uuid' => __('messages.checkInSelectLocation.outsideHours', [
                'name' => $location->name,
                'open' => '9:00 AM',
                'close' => '5:00 PM EDT',
            ]),
        ]);

});

it('rejects check-in when the driver is farther than the allowed yard radius', function (): void {
    $location = Location::factory()
        ->for(Address::factory()->state([
            'latitude' => 26.142,
            'longitude' => -80.478,
        ]))
        ->create([
            'timezone' => 'UTC',
            'max_distance_allowed' => 5,
        ]);

    $this->from(route('checkIn.selectLocation'))
        ->withSession([
            'userCoords' => [
                'latitude' => 40.7128,
                'longitude' => -74.006,
                'storedAt' => now()->timestamp,
            ],
        ])
        ->post(route('checkIn.form', $location))
        ->assertRedirect()
        ->assertSessionHasErrors([
            'uuid' => __('messages.checkInSelectLocation.tooFar', [
                'name' => $location->name,
                'maxDistance' => 5,
                'userDistance' => 1072.9,
            ]),
        ]);
});
