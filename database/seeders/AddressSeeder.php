<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'street1' => '1751 SW 8th Street',
                'street2' => null,
                'city' => 'Pompano Beach',
                'state' => 'FL',
                'zip_code' => '33069',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 26.220294,
                'longitude' => -80.145726,
            ],
            [
                'street1' => '1 Saville Avenue',
                'street2' => null,
                'city' => 'Eddystone',
                'state' => 'PA',
                'zip_code' => '19022',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 39.854992,
                'longitude' => -75.339521,
            ],
            [
                'street1' => '5655 Arcturus Avenue',
                'street2' => null,
                'city' => 'Oxnard',
                'state' => 'CA',
                'zip_code' => '93033',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 34.156842,
                'longitude' => -119.166402,
            ],
            [
                'street1' => '4203 Cedar Boulevard',
                'street2' => null,
                'city' => 'Baytown',
                'state' => 'TX',
                'zip_code' => '77523',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 29.721309,
                'longitude' => -94.917327,
            ],
            [
                'street1' => '6879 N. Washoe Avenue',
                'street2' => null,
                'city' => 'Firebaugh',
                'state' => 'CA',
                'zip_code' => '93622',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 36.836862,
                'longitude' => -120.460507,
            ],
            [
                'street1' => '51240 Valley Road',
                'street2' => null,
                'city' => 'Aguila',
                'state' => 'AZ',
                'zip_code' => '85320',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 33.944973,
                'longitude' => -113.170952,
            ],
            [
                'street1' => '9254 N. Ralston Road',
                'street2' => null,
                'city' => 'Maricopa',
                'state' => 'AZ',
                'zip_code' => '85139',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 32.963338,
                'longitude' => -112.118914,
            ],
            [
                'street1' => '53931 W. Lower Buckey Road',
                'street2' => null,
                'city' => 'Tonopah',
                'state' => 'AZ',
                'zip_code' => '85354',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 33.420392,
                'longitude' => -113.200520,
            ],
            [
                'street1' => '850 Work Street',
                'street2' => null,
                'city' => 'Salinas',
                'state' => 'CA',
                'zip_code' => '93901',
                'zip_code_4' => '0000',
                'country' => 'US',
                'latitude' => 36.663923,
                'longitude' => -121.634146,
            ],
        ];

        DB::table('addresses')->insert($addresses);
    }
}
