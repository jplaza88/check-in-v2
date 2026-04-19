<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Location;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Location>
 */
final class LocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'address_id' => Address::factory(),
            'max_distance_allowed' => 50,
            'name' => fake()->company(),
            'abbreviation' => fake()->unique()->lexify('???'),
            'timezone' => 'UTC',
            'phone' => fake()->phoneNumber(),
            'phone_ext' => null,
            'email' => fake()->safeEmail(),
            'is_active' => true,
            'is_checkins_enabled' => true,
            'is_appointments_enabled' => false,
            'additional_fields' => false,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Location $location): void {
            Schedule::factory()->for($location)->create();

            if ($location->is_appointments_enabled) {
                Schedule::factory()->forAppointments()->for($location)->create();
            }
        });
    }

    public function appointmentsOnly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_checkins_enabled' => false,
            'is_appointments_enabled' => true,
        ]);
    }
}
