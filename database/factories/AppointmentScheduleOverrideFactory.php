<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentScheduleOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentScheduleOverride>
 */
final class AppointmentScheduleOverrideFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'override_date' => fake()->date(),
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
            'is_closed' => false,
            'reason' => fake()->sentence(),
        ];
    }

    public function onDate(string $date): static
    {
        return $this->state(fn (): array => [
            'override_date' => $date,
        ]);
    }

    public function closure(): static
    {
        return $this->state(fn (): array => [
            'open_time' => null,
            'close_time' => null,
            'is_closed' => true,
        ]);
    }

    public function specialHours(string $openTime, string $closeTime): static
    {
        return $this->state(fn (): array => [
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'is_closed' => false,
        ]);
    }
}
