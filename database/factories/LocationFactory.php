<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Address;
use App\Models\Location;
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
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Location $location): void {
            if ($location->is_checkins_enabled) {
                foreach (range(0, 6) as $dayOfWeek) {
                    $location->checkinSchedule()->create([
                        'day_of_week' => $dayOfWeek,
                        'open_time' => '00:00:00',
                        'close_time' => '23:59:59',
                    ]);
                }
            }

            if ($location->is_appointments_enabled) {
                foreach (range(0, 6) as $dayOfWeek) {
                    $location->appointmentSchedule()->create([
                        'day_of_week' => $dayOfWeek,
                        'open_time' => '00:00:00',
                        'close_time' => '23:59:59',
                    ]);
                }
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

    /**
     * No open hours on any day (distribution center appears closed for check-in).
     */
    public function checkinClosedAllWeek(): static
    {
        return $this->afterCreating(function (Location $location): void {
            $location->checkinSchedule()->delete();
        });
    }

    /**
     * Weekdays 9am-5pm in the location timezone; weekends have no hours.
     */
    public function checkinWeekdayWindow(): static
    {
        return $this->afterCreating(function (Location $location): void {
            $location->checkinSchedule()->delete();

            foreach (range(1, 5) as $dayOfWeek) {
                $location->checkinSchedule()->create([
                    'day_of_week' => $dayOfWeek,
                    'open_time' => '09:00:00',
                    'close_time' => '17:00:00',
                ]);
            }
        });
    }
}
