<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ScheduleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $locationIds = DB::table('locations')->pluck('id');

        $default = [
            'sunday_open' => null,
            'sunday_close' => null,
            'monday_open' => '07:00:00',
            'monday_close' => '17:00:00',
            'tuesday_open' => '07:00:00',
            'tuesday_close' => '17:00:00',
            'wednesday_open' => '07:00:00',
            'wednesday_close' => '17:00:00',
            'thursday_open' => '07:00:00',
            'thursday_close' => '17:00:00',
            'friday_open' => '07:00:00',
            'friday_close' => '17:00:00',
            'saturday_open' => null,
            'saturday_close' => null,
        ];

        foreach ($locationIds as $locationId) {
            DB::table('schedules')->insert([
                ...$default,
                'location_id' => $locationId,
                'type' => ScheduleType::CheckIn,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('schedules')->insert([
                ...$default,
                'location_id' => $locationId,
                'type' => ScheduleType::Appointment,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
