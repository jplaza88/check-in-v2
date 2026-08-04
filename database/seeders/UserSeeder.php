<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

final class UserSeeder extends Seeder
{
    public const string ADMIN_EMAIL = 'admin@juanplaza.dev';

    /**
     * Seeds a known-credential account, so it refuses to run in production.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('UserSeeder skipped: refusing to seed a known-credential account in production.');

            return;
        }

        $password = config('app.seed_admin_password');

        User::query()->firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => 'Juan Plaza',
                'password' => $password,
                'cellphone' => '+15551234567',
                'drivers_license_number' => 'D4821955',
                'drivers_license_state' => 'AZ',
                'drivers_license_expiration_date' => Date::now()->addYears(3)->format('Y-m-d'),
            ],
        )->forceFill(['email_verified_at' => Date::now()])->save();
    }
}
