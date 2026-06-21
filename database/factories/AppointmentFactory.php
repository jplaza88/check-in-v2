<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

final class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'scheduled_for' => Date::now(),
            'drivers_name' => $this->faker->name(),
            'drivers_cellphone' => $this->faker->word(),
            'status' => $this->faker->randomElement(AppointmentStatus::cases()),
            'cancelled_at' => Date::now(),
            'cancelled_reason' => $this->faker->word(),
            'claimed_via' => $this->faker->word(),
            'claimed_at' => Date::now(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'location_id' => Location::factory(),
            'user_id' => null,
        ];
    }
}
