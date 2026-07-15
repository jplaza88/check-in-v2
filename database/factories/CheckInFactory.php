<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TrailerChute;
use App\Models\CheckIn;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

final class CheckInFactory extends Factory
{
    protected $model = CheckIn::class;

    public function definition(): array
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        return [
            'uuid' => $this->faker->uuid(),
            'reference_number' => implode('', array_map(
                fn (): string => $chars[random_int(0, mb_strlen($chars) - 1)],
                range(1, 8),
            )),
            'status' => 'pending',
            'erp_status' => 'pending',
            'customer' => $this->faker->company(),
            'destination_city' => $this->faker->city(),
            'destination_state' => $this->faker->stateAbbr(),
            'destination_country' => $this->faker->countryCode(),
            'truck_name' => $this->faker->word(),
            'truck_plate' => mb_strtoupper($this->faker->bothify('???###')),
            'truck_color' => $this->faker->randomElement((array) config('app.truck_colors')),
            'truck_plate_state' => $this->faker->stateAbbr(),
            'truck_plate_country' => $this->faker->countryCode(),
            'trailer_plate' => mb_strtoupper($this->faker->bothify('???###')),
            'trailer_plate_state' => $this->faker->stateAbbr(),
            'trailer_plate_country' => $this->faker->countryCode(),
            'trailer_chute' => $this->faker->randomElement(TrailerChute::cases()),
            'empty_weight_lbs' => null,
            'drivers_name' => $this->faker->name(),
            'drivers_cellphone' => '+1'.$this->faker->numerify('##########'),
            'drivers_email' => $this->faker->optional()->safeEmail(),
            'drivers_license_number' => mb_strtoupper($this->faker->bothify('??######')),
            'drivers_license_expiration_date' => null,
            'drivers_license_state' => $this->faker->stateAbbr(),
            'drivers_license_country' => $this->faker->countryCode(),
            'loading_instructions' => null,
            'locale' => $this->faker->randomElement(['en', 'es']),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'location_id' => Location::factory(),
            'user_id' => null,
            'appointment_id' => null,
        ];
    }
}
