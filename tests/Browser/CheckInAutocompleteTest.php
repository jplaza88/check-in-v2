<?php

declare(strict_types=1);

use App\Models\Address;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use App\Models\State;

it('autocompletes the destination city on the check-in form', function (): void {
    $us = Country::query()->create(['short_name' => 'US', 'name' => 'United States', 'phone_code' => 1, 'active' => 1]);
    $arizona = State::query()->create(['short_name' => 'AZ', 'name' => 'Arizona', 'country_id' => $us->id]);
    foreach (['Phoenix', 'Phoenixville', 'Phoenix Valley'] as $name) {
        City::query()->create(['name' => $name, 'state_id' => $arizona->id]);
    }

    $address = Address::factory()->create(['latitude' => 33.4484, 'longitude' => -112.0740]);
    Location::factory()->create([
        'address_id' => $address->id,
        'name' => 'Eddystone Terminal',
        'max_distance_allowed' => 500,
        'timezone' => 'America/Phoenix',
    ]);

    // Walk the real gated flow: geolocation → dismiss the info modal → clear the
    // proximity gate → land on the check-in form.
    $page = visit(route('checkIn.selectLocation'))->geolocation(33.4484, -112.0740);

    $page->assertSee('Eddystone Terminal')
        ->click('OK')
        ->click('Eddystone Terminal');

    $page->assertSee('Destination City');
    $page->fill('#destination_city', 'Pho');
    $page->assertSee('Phoenix, Arizona');

    // Pick the first suggestion (the option row commits on mousedown).
    $page->script('document.querySelector("[role=option]").dispatchEvent(new MouseEvent("mousedown", { bubbles: true, cancelable: true }))');

    // State + country auto-populate into read-only fields.
    $page->assertSee('Arizona')
        ->assertSee('United States')
        ->assertNoJavascriptErrors();
});
