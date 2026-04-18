<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ScheduleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ScheduleExceptionSeeder extends Seeder
{
    public function run(): void
    {
        $exceptions = [
            [
                'location_id' => 1,
                'date' => '2026-04-06',
                'open' => '07:00:00',
                'close' => '12:00:00',
                'is_closed' => false,
                'reason' => 'testing 123',
            ],
        ];

        foreach ($exceptions as $exception) {
            DB::table('schedule_exceptions')->insert([
                ...$exception,
                'type' => ScheduleType::CheckIn,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('schedule_exceptions')->insert([
                ...$exception,
                'type' => ScheduleType::Appointment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
