<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountryStateCitySeeder extends Seeder
{
    /**
     * @throws FileNotFoundException
     */
    public function run(): void
    {
        Country::create([
            'id' => 38,
            'short_name' => 'CA',
            'name' => 'Canada',
            'phone_code' => 1,
            'active' => 1,
        ]);

        Country::create([
            'id' => 142,
            'short_name' => 'MX',
            'name' => 'Mexico',
            'phone_code' => 52,
            'active' => 0,
        ]);

        Country::create([
            'id' => 231,
            'short_name' => 'US',
            'name' => 'United States',
            'phone_code' => 1,
            'active' => 1,
        ]);

        // Canada (country_id = 38)
        State::insert([
            ['id' => 663, 'short_name' => null, 'name' => 'Alberta', 'country_id' => 38],
            ['id' => 664, 'short_name' => null, 'name' => 'British Columbia', 'country_id' => 38],
            ['id' => 665, 'short_name' => null, 'name' => 'Manitoba', 'country_id' => 38],
            ['id' => 666, 'short_name' => null, 'name' => 'New Brunswick', 'country_id' => 38],
            ['id' => 667, 'short_name' => null, 'name' => 'Newfoundland and Labrador', 'country_id' => 38],
            ['id' => 668, 'short_name' => null, 'name' => 'Northwest Territories', 'country_id' => 38],
            ['id' => 669, 'short_name' => null, 'name' => 'Nova Scotia', 'country_id' => 38],
            ['id' => 670, 'short_name' => null, 'name' => 'Nunavut', 'country_id' => 38],
            ['id' => 671, 'short_name' => null, 'name' => 'Ontario', 'country_id' => 38],
            ['id' => 672, 'short_name' => null, 'name' => 'Prince Edward Island', 'country_id' => 38],
            ['id' => 673, 'short_name' => null, 'name' => 'Quebec', 'country_id' => 38],
            ['id' => 674, 'short_name' => null, 'name' => 'Saskatchewan', 'country_id' => 38],
            ['id' => 675, 'short_name' => null, 'name' => 'Yukon', 'country_id' => 38],
        ]);

        // Mexico (country_id = 142)
        State::insert([
            ['id' => 2427, 'short_name' => null, 'name' => 'Aguascalientes', 'country_id' => 142],
            ['id' => 2428, 'short_name' => null, 'name' => 'Baja California', 'country_id' => 142],
            ['id' => 2429, 'short_name' => null, 'name' => 'Baja California Sur', 'country_id' => 142],
            ['id' => 2430, 'short_name' => null, 'name' => 'Campeche', 'country_id' => 142],
            ['id' => 2431, 'short_name' => null, 'name' => 'Chiapas', 'country_id' => 142],
            ['id' => 2432, 'short_name' => null, 'name' => 'Chihuahua', 'country_id' => 142],
            ['id' => 2433, 'short_name' => null, 'name' => 'Coahuila', 'country_id' => 142],
            ['id' => 2434, 'short_name' => null, 'name' => 'Colima', 'country_id' => 142],
            ['id' => 2435, 'short_name' => null, 'name' => 'Distrito Federal', 'country_id' => 142],
            ['id' => 2436, 'short_name' => null, 'name' => 'Durango', 'country_id' => 142],
            ['id' => 2437, 'short_name' => null, 'name' => 'Estado de Mexico', 'country_id' => 142],
            ['id' => 2438, 'short_name' => null, 'name' => 'Guanajuato', 'country_id' => 142],
            ['id' => 2439, 'short_name' => null, 'name' => 'Guerrero', 'country_id' => 142],
            ['id' => 2440, 'short_name' => null, 'name' => 'Hidalgo', 'country_id' => 142],
            ['id' => 2441, 'short_name' => null, 'name' => 'Jalisco', 'country_id' => 142],
            ['id' => 2442, 'short_name' => null, 'name' => 'Mexico', 'country_id' => 142],
            ['id' => 2443, 'short_name' => null, 'name' => 'Michoacan', 'country_id' => 142],
            ['id' => 2444, 'short_name' => null, 'name' => 'Morelos', 'country_id' => 142],
            ['id' => 2445, 'short_name' => null, 'name' => 'Nayarit', 'country_id' => 142],
            ['id' => 2446, 'short_name' => null, 'name' => 'Nuevo Leon', 'country_id' => 142],
            ['id' => 2447, 'short_name' => null, 'name' => 'Oaxaca', 'country_id' => 142],
            ['id' => 2448, 'short_name' => null, 'name' => 'Puebla', 'country_id' => 142],
            ['id' => 2449, 'short_name' => null, 'name' => 'Queretaro', 'country_id' => 142],
            ['id' => 2450, 'short_name' => null, 'name' => 'Quintana Roo', 'country_id' => 142],
            ['id' => 2451, 'short_name' => null, 'name' => 'San Luis Potosi', 'country_id' => 142],
            ['id' => 2452, 'short_name' => null, 'name' => 'Sinaloa', 'country_id' => 142],
            ['id' => 2453, 'short_name' => null, 'name' => 'Sonora', 'country_id' => 142],
            ['id' => 2454, 'short_name' => null, 'name' => 'Tabasco', 'country_id' => 142],
            ['id' => 2455, 'short_name' => null, 'name' => 'Tamaulipas', 'country_id' => 142],
            ['id' => 2456, 'short_name' => null, 'name' => 'Tlaxcala', 'country_id' => 142],
            ['id' => 2457, 'short_name' => null, 'name' => 'Veracruz', 'country_id' => 142],
            ['id' => 2458, 'short_name' => null, 'name' => 'Yucatan', 'country_id' => 142],
            ['id' => 2459, 'short_name' => null, 'name' => 'Zacatecas', 'country_id' => 142],
        ]);

        // United States (country_id = 231)
        State::insert([
            ['id' => 3919, 'short_name' => 'AL', 'name' => 'Alabama', 'country_id' => 231],
            ['id' => 3920, 'short_name' => 'AK', 'name' => 'Alaska', 'country_id' => 231],
            ['id' => 3921, 'short_name' => 'AZ', 'name' => 'Arizona', 'country_id' => 231],
            ['id' => 3922, 'short_name' => 'AR', 'name' => 'Arkansas', 'country_id' => 231],
            ['id' => 3924, 'short_name' => 'CA', 'name' => 'California', 'country_id' => 231],
            ['id' => 3926, 'short_name' => 'CO', 'name' => 'Colorado', 'country_id' => 231],
            ['id' => 3927, 'short_name' => 'CT', 'name' => 'Connecticut', 'country_id' => 231],
            ['id' => 3928, 'short_name' => 'DE', 'name' => 'Delaware', 'country_id' => 231],
            ['id' => 3929, 'short_name' => 'DC', 'name' => 'District of Columbia', 'country_id' => 231],
            ['id' => 3930, 'short_name' => 'FL', 'name' => 'Florida', 'country_id' => 231],
            ['id' => 3931, 'short_name' => 'GA', 'name' => 'Georgia', 'country_id' => 231],
            ['id' => 3932, 'short_name' => 'HI', 'name' => 'Hawaii', 'country_id' => 231],
            ['id' => 3933, 'short_name' => 'ID', 'name' => 'Idaho', 'country_id' => 231],
            ['id' => 3934, 'short_name' => 'IL', 'name' => 'Illinois', 'country_id' => 231],
            ['id' => 3935, 'short_name' => 'IN', 'name' => 'Indiana', 'country_id' => 231],
            ['id' => 3936, 'short_name' => 'IA', 'name' => 'Iowa', 'country_id' => 231],
            ['id' => 3937, 'short_name' => 'KS', 'name' => 'Kansas', 'country_id' => 231],
            ['id' => 3938, 'short_name' => 'KY', 'name' => 'Kentucky', 'country_id' => 231],
            ['id' => 3939, 'short_name' => 'LA', 'name' => 'Louisiana', 'country_id' => 231],
            ['id' => 3941, 'short_name' => 'ME', 'name' => 'Maine', 'country_id' => 231],
            ['id' => 3942, 'short_name' => 'MD', 'name' => 'Maryland', 'country_id' => 231],
            ['id' => 3943, 'short_name' => 'MA', 'name' => 'Massachusetts', 'country_id' => 231],
            ['id' => 3945, 'short_name' => 'MI', 'name' => 'Michigan', 'country_id' => 231],
            ['id' => 3946, 'short_name' => 'MN', 'name' => 'Minnesota', 'country_id' => 231],
            ['id' => 3947, 'short_name' => 'MS', 'name' => 'Mississippi', 'country_id' => 231],
            ['id' => 3948, 'short_name' => 'MO', 'name' => 'Missouri', 'country_id' => 231],
            ['id' => 3949, 'short_name' => 'MT', 'name' => 'Montana', 'country_id' => 231],
            ['id' => 3950, 'short_name' => 'NE', 'name' => 'Nebraska', 'country_id' => 231],
            ['id' => 3951, 'short_name' => 'NV', 'name' => 'Nevada', 'country_id' => 231],
            ['id' => 3952, 'short_name' => 'NH', 'name' => 'New Hampshire', 'country_id' => 231],
            ['id' => 3953, 'short_name' => 'NJ', 'name' => 'New Jersey', 'country_id' => 231],
            ['id' => 3955, 'short_name' => 'NM', 'name' => 'New Mexico', 'country_id' => 231],
            ['id' => 3956, 'short_name' => 'NY', 'name' => 'New York', 'country_id' => 231],
            ['id' => 3957, 'short_name' => 'NC', 'name' => 'North Carolina', 'country_id' => 231],
            ['id' => 3958, 'short_name' => 'ND', 'name' => 'North Dakota', 'country_id' => 231],
            ['id' => 3959, 'short_name' => 'OH', 'name' => 'Ohio', 'country_id' => 231],
            ['id' => 3960, 'short_name' => 'OK', 'name' => 'Oklahoma', 'country_id' => 231],
            ['id' => 3962, 'short_name' => 'OR', 'name' => 'Oregon', 'country_id' => 231],
            ['id' => 3963, 'short_name' => 'PA', 'name' => 'Pennsylvania', 'country_id' => 231],
            ['id' => 3965, 'short_name' => 'RI', 'name' => 'Rhode Island', 'country_id' => 231],
            ['id' => 3966, 'short_name' => 'SC', 'name' => 'South Carolina', 'country_id' => 231],
            ['id' => 3967, 'short_name' => 'SD', 'name' => 'South Dakota', 'country_id' => 231],
            ['id' => 3969, 'short_name' => 'TN', 'name' => 'Tennessee', 'country_id' => 231],
            ['id' => 3970, 'short_name' => 'TX', 'name' => 'Texas', 'country_id' => 231],
            ['id' => 3972, 'short_name' => 'UT', 'name' => 'Utah', 'country_id' => 231],
            ['id' => 3973, 'short_name' => 'VT', 'name' => 'Vermont', 'country_id' => 231],
            ['id' => 3974, 'short_name' => 'VA', 'name' => 'Virginia', 'country_id' => 231],
            ['id' => 3975, 'short_name' => 'WA', 'name' => 'Washington', 'country_id' => 231],
            ['id' => 3976, 'short_name' => 'WV', 'name' => 'West Virginia', 'country_id' => 231],
            ['id' => 3977, 'short_name' => 'WI', 'name' => 'Wisconsin', 'country_id' => 231],
            ['id' => 3978, 'short_name' => 'WY', 'name' => 'Wyoming', 'country_id' => 231],
        ]);

        $path = database_path('sql/cities_202506061736.sql');
        DB::unprepared(File::get($path));
    }
}
