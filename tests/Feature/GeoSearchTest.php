<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Country;
use App\Models\State;

function seedGeoFixtures(): void
{
    $us = Country::query()->create(['short_name' => 'US', 'name' => 'United States', 'phone_code' => 1, 'active' => 1]);
    $arizona = State::query()->create(['short_name' => 'AZ', 'name' => 'Arizona', 'country_id' => $us->id]);

    City::query()->create(['name' => 'Phoenix', 'state_id' => $arizona->id]);
    City::query()->create(['name' => 'Phoenixville', 'state_id' => $arizona->id]);
    City::query()->create(['name' => 'Tucson', 'state_id' => $arizona->id]);
}

it('searches cities by case-insensitive prefix with state + country', function (): void {
    seedGeoFixtures();

    $this->getJson(route('geo.cities', ['q' => 'pho']))
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment([
            'city' => 'Phoenix',
            'state' => 'Arizona',
            'stateCode' => 'AZ',
            'country' => 'United States',
            'countryCode' => 'US',
        ]);

    // Same results regardless of case.
    $this->getJson(route('geo.cities', ['q' => 'PHO']))->assertOk()->assertJsonCount(2);
});

it('returns nothing for queries shorter than three characters', function (): void {
    seedGeoFixtures();

    $this->getJson(route('geo.cities', ['q' => 'ph']))->assertOk()->assertExactJson([]);
    $this->getJson(route('geo.cities', ['q' => '']))->assertOk()->assertExactJson([]);
});

it('searches states with their country', function (): void {
    seedGeoFixtures();

    $this->getJson(route('geo.states', ['q' => 'ari']))
        ->assertOk()
        ->assertJsonFragment(['state' => 'Arizona', 'countryCode' => 'US']);
});
