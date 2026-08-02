<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CheckInErpStatus;
use App\Enums\CheckInStatus;
use App\Enums\TrailerChute;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<CheckIn>
 */
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
            'status' => CheckInStatus::Pending,
            'erp_status' => CheckInErpStatus::Pending,
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

    /**
     * Attach the check-in to a driver and mirror their details onto the record,
     * the way CreateCheckInAction does for an authenticated driver.
     *
     * Note this deliberately leaves claimed_at/claimed_via alone: those are only
     * stamped by the post-registration claim flow. Use claimed() for that case.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => array_filter([
            'user_id' => $user->id,
            'drivers_name' => $user->name,
            'drivers_email' => $user->email,
            'drivers_cellphone' => $user->cellphone,
            'drivers_license_number' => $user->drivers_license_number,
            'drivers_license_state' => $user->drivers_license_state,
            'drivers_license_expiration_date' => $user->drivers_license_expiration_date,
        ], fn (mixed $value): bool => $value !== null));
    }

    public function atLocation(Location $location): static
    {
        return $this->state(fn (array $attributes): array => [
            'location_id' => $location->id,
        ]);
    }

    /**
     * Backdate the record. Sets updated_at alongside created_at so the two never
     * disagree, which would otherwise break any ordering or cache key built on it.
     */
    public function createdAt(CarbonInterface $when): static
    {
        return $this->state(fn (array $attributes): array => [
            'created_at' => $when,
            'updated_at' => $when,
        ]);
    }

    public function claimed(string $via = 'email_verification'): static
    {
        return $this->state(fn (array $attributes): array => [
            'claimed_at' => Date::now(),
            'claimed_via' => $via,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CheckInStatus::Pending,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CheckInStatus::Completed,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => CheckInStatus::Cancelled,
        ]);
    }
}
