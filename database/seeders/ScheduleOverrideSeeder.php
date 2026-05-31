<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ScheduleOverrideSeeder extends Seeder
{
    public function run(): void
    {
        $overrides = [
            [
                'location_id' => 1,
                'date' => '2026-04-06',
                'open_time' => '07:00:00',
                'close_time' => '12:00:00',
                'is_closed' => false,
                'reason' => 'testing 123',
            ],
        ];

        foreach ($overrides as $override) {
            DB::table('checkin_schedule_overrides')->insert([
                ...$override,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('appointment_schedule_overrides')->insert([
                ...$override,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
