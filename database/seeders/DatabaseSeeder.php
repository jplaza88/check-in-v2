<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        $this->call([
            CountryStateCitySeeder::class,
            AddressSeeder::class,
            LocationSeeder::class,
            ScheduleSeeder::class,
            ScheduleOverrideSeeder::class,
        ]);

        // Demo data only. The reference seeders above are production-safe;
        // these create a known-credential account and fake driver history.
        if (! app()->isProduction()) {
            $this->call([
                UserSeeder::class,
                DriverHistorySeeder::class,
            ]);
        }
    }
}
