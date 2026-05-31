<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $locationIds = DB::table('locations')->pluck('id');
        $now = now();

        // Carbon: 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $weekdaySchedule = [
            1 => ['open_time' => '07:00:00', 'close_time' => '17:00:00'],
            2 => ['open_time' => '07:00:00', 'close_time' => '17:00:00'],
            3 => ['open_time' => '07:00:00', 'close_time' => '17:00:00'],
            4 => ['open_time' => '07:00:00', 'close_time' => '17:00:00'],
            5 => ['open_time' => '07:00:00', 'close_time' => '17:00:00'],
        ];

        $rows = [];

        foreach ($locationIds as $locationId) {
            foreach ($weekdaySchedule as $dayOfWeek => $hours) {
                $rows[] = [
                    'location_id' => $locationId,
                    'day_of_week' => $dayOfWeek,
                    ...$hours,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('checkin_schedules')->insert($rows);
        DB::table('appointment_schedules')->insert($rows);
    }
}
