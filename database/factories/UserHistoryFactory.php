<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserHistoryEvent;
use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class UserHistoryFactory extends Factory
{
    protected $model = UserHistory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // User does not use HasFactory, so build the owner by hand.
            'user_id' => fn (): int => User::query()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => 'password',
            ])->id,
            'event' => UserHistoryEvent::ProfileUpdated,
            'changes' => null,
        ];
    }
}
