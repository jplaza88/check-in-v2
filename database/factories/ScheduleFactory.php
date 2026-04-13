<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hours = [];

        foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day) {
            $hours["{$day}_open"] = '00:00:00';
            $hours["{$day}_close"] = '23:59:59';
        }

        return $hours;
    }

    /**
     * No open hours on any day (distribution center appears closed for check-in).
     */
    public function closedEveryDay(): static
    {
        return $this->state(function (): array {
            $hours = [];

            foreach (['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day) {
                $hours["{$day}_open"] = null;
                $hours["{$day}_close"] = null;
            }

            return $hours;
        });
    }

    /**
     * Weekdays 9am–5pm in the location timezone; weekends have no hours.
     */
    public function weekdayPickupWindow(): static
    {
        return $this->state(function (): array {
            $hours = [];

            foreach (['sunday', 'saturday'] as $day) {
                $hours["{$day}_open"] = null;
                $hours["{$day}_close"] = null;
            }

            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
                $hours["{$day}_open"] = '09:00:00';
                $hours["{$day}_close"] = '17:00:00';
            }

            return $hours;
        });
    }
}
