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
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        return [
            'uuid' => $this->faker->uuid(),
            'reference_number' => implode('', array_map(
                fn (): string => $chars[random_int(0, mb_strlen($chars) - 1)],
                range(1, 8),
            )),
            'scheduled_for' => Date::now(),
            'drivers_name' => $this->faker->name(),
            'drivers_cellphone' => '+1'.$this->faker->numerify('##########'),
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
