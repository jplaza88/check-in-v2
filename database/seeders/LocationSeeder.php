<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'address_id' => 1,
                'name' => 'Pompano Beach, FL - Sol Group Marketing',
                'abbreviation' => 'pmp',
                'timezone' => 'America/New_York',
                'max_distance_allowed' => 5,
                'phone' => '(954) 781-0003',
                'phone_ext' => null,
                'email' => 'ci-pompano@solgroup-marketing.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 2,
                'name' => 'Eddystone, PA - Penn Terminals',
                'abbreviation' => 'ppt',
                'timezone' => 'America/New_York',
                'max_distance_allowed' => 5,
                'phone' => '(954) 781-0003',
                'phone_ext' => null,
                'email' => 'ci-pennterminal@solgroup-marketing.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 3,
                'name' => 'Oxnard, CA - Channel Islands Logistics',
                'abbreviation' => 'cil',
                'timezone' => 'America/Los_Angeles',
                'max_distance_allowed' => 5,
                'phone' => '(954) 781-0003',
                'phone_ext' => null,
                'email' => 'ci-channel@solgroup-marketing.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 4,
                'name' => 'Baytown, TX - Foremost Fresh Direct',
                'abbreviation' => 'ffd',
                'timezone' => 'America/Chicago',
                'max_distance_allowed' => 5,
                'phone' => '(954) 781-0003',
                'phone_ext' => null,
                'email' => 'ci-channel@solgroup-marketing.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 5,
                'name' => 'Firebaugh, CA',
                'abbreviation' => 'fir',
                'timezone' => 'America/Los_Angeles',
                'max_distance_allowed' => 5,
                'phone' => '(480) 998-1444',
                'phone_ext' => null,
                'email' => 'ci-firebaugh@martorifarms.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 6,
                'name' => 'Aguila, AZ',
                'abbreviation' => 'agu',
                'timezone' => 'America/Phoenix',
                'max_distance_allowed' => 5,
                'phone' => '(480) 998-1444',
                'phone_ext' => null,
                'email' => 'ci-aguila@martorifarms.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 7,
                'name' => 'Maricopa, AZ',
                'abbreviation' => 'mar',
                'timezone' => 'America/Phoenix',
                'max_distance_allowed' => 5,
                'phone' => '(480) 998-1444',
                'phone_ext' => null,
                'email' => 'ci-maricopa@martorifarms.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 8,
                'name' => 'Tonopah, AZ - Court House',
                'abbreviation' => 'crt',
                'timezone' => 'America/Phoenix',
                'max_distance_allowed' => 5,
                'phone' => '(480) 998-1444',
                'phone_ext' => null,
                'email' => 'ci-courthouse@martorifarms.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
            [
                'address_id' => 9,
                'name' => 'Salinas, CA',
                'abbreviation' => 'sal',
                'timezone' => 'America/Los_Angeles',
                'max_distance_allowed' => 5,
                'phone' => '(480) 998-1444',
                'phone_ext' => null,
                'email' => 'ci-salinas@martorifarms.com',
                'is_active' => true,
                'is_checkins_enabled' => true,
                'is_appointments_enabled' => true,
                'additional_fields' => false,
            ],
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert([
                ...$location,
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
