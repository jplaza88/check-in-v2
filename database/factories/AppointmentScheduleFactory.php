<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppointmentSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentSchedule>
 */
final class AppointmentScheduleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day_of_week' => fake()->numberBetween(0, 6),
            'open_time' => '00:00:00',
            'close_time' => '23:59:59',
        ];
    }

    public function forDay(int $dayOfWeek): static
    {
        return $this->state(fn (): array => [
            'day_of_week' => $dayOfWeek,
        ]);
    }

    public function hours(string $openTime, string $closeTime): static
    {
        return $this->state(fn (): array => [
            'open_time' => $openTime,
            'close_time' => $closeTime,
        ]);
    }
}
