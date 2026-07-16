<?php

declare(strict_types=1);

use App\Models\Location;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia;

beforeEach(fn () => Cache::flush());

it('renders the weekly schedule with seven days per location', function (): void {
    Location::factory()->create(['name' => 'Everglades Watermelons']);

    $this->get(route('schedule'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Schedule')
            ->has('locations', 1)
            ->where('locations.0.name', 'Everglades Watermelons')
            ->has('locations.0.checkInWeek', 7)
            ->has('locations.0.appointmentWeek', 7)
            ->has('locations.0.checkInWeek.0.weekday')
            ->has('locations.0.timezoneAbbr'));
});

it('excludes inactive locations from the schedule', function (): void {
    Location::factory()->create(['name' => 'Active Farm']);
    Location::factory()->create(['name' => 'Inactive Farm', 'is_active' => false]);

    $this->get(route('schedule'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->component('Schedule')
            ->has('locations', 1)
            ->where('locations.0.name', 'Active Farm'));
});

it('serves a warm cached schedule without a serialization error', function (): void {
    // The array store used elsewhere keeps objects in memory and never
    // serializes, which hides cache-hit deserialization bugs. Force a store
    // that actually round-trips through unserialize() so the DTOs must be
    // listed in cache.serializable_classes or come back as an incomplete class.
    config()->set('cache.default', 'file');
    Cache::store('file')->flush();

    Location::factory()->create(['name' => 'Warm Cache Farm']);

    // First request primes the cache (miss); the second reads it back (hit),
    // which is where __PHP_Incomplete_Class previously surfaced as a 500.
    $this->get(route('schedule'))->assertSuccessful();

    $this->get(route('schedule'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
            ->has('locations', 1)
            ->where('locations.0.name', 'Warm Cache Farm'));

    Cache::store('file')->flush();
});

it('flushes the cached schedule when a location changes', function (): void {
    $location = Location::factory()->create(['name' => 'Cache Test Farm']);

    // Prime the cache.
    $this->get(route('schedule'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page->has('locations', 1));

    // An admin change should invalidate the cache via the model observer.
    $location->update(['is_active' => false]);

    $this->get(route('schedule'))
        ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page->has('locations', 0));
});
