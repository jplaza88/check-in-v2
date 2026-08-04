<?php

declare(strict_types=1);

use App\Enums\HistoryPeriod;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use Database\Seeders\DriverHistorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;

function seedDriverHistory(): User
{
    Location::factory()->count(3)->create(['timezone' => 'America/Phoenix']);

    (new UserSeeder)->run();
    (new DriverHistorySeeder)->run();

    return User::query()->where('email', UserSeeder::ADMIN_EMAIL)->firstOrFail();
}

it('seeds a verified admin driver with a license on file', function (): void {
    (new UserSeeder)->run();

    $user = User::query()->where('email', UserSeeder::ADMIN_EMAIL)->firstOrFail();

    // Mirrors the seeder's own fallback so a local SEED_ADMIN_PASSWORD does
    // not break this assertion.
    $expected = env('SEED_ADMIN_PASSWORD') ?: 'password';

    expect($user->email_verified_at)->not->toBeNull()
        ->and($user->drivers_license_number)->not->toBeNull()
        ->and($user->cellphone)->not->toBeNull()
        ->and(Hash::check($expected, $user->getAuthPassword()))->toBeTrue();
});

it('is idempotent when run twice', function (): void {
    seedDriverHistory();

    $checkIns = CheckIn::query()->count();
    $appointments = Appointment::query()->count();

    (new UserSeeder)->run();
    (new DriverHistorySeeder)->run();

    expect(User::query()->where('email', UserSeeder::ADMIN_EMAIL)->count())->toBe(1)
        ->and(CheckIn::query()->count())->toBe($checkIns)
        ->and(Appointment::query()->count())->toBe($appointments);
});

it('spreads history so each period preset returns a different set', function (): void {
    $driver = seedDriverHistory();

    $countCheckIns = fn (HistoryPeriod $period): int => CheckIn::query()
        ->where('user_id', $driver->id)
        ->when($period->since(), fn ($q) => $q->where('created_at', '>=', $period->since()))
        ->count();

    $thirty = $countCheckIns(HistoryPeriod::ThirtyDays);
    $ninety = $countCheckIns(HistoryPeriod::NinetyDays);
    $year = $countCheckIns(HistoryPeriod::TwelveMonths);
    $all = $countCheckIns(HistoryPeriod::AllTime);

    expect($thirty)->toBeLessThan($ninety)
        ->and($ninety)->toBeLessThan($year)
        ->and($year)->toBeLessThan($all)
        ->and($all)->toBeGreaterThan(15);
});

it('seeds upcoming appointments and gives every record purchase orders', function (): void {
    $driver = seedDriverHistory();

    $upcoming = Appointment::query()
        ->where('user_id', $driver->id)
        ->where('scheduled_for', '>', Date::now())
        ->count();

    expect($upcoming)->toBeGreaterThan(0)
        ->and(CheckIn::query()->where('user_id', $driver->id)->has('purchaseOrders')->count())
        ->toBe(CheckIn::query()->where('user_id', $driver->id)->count());
});

it('covers every status so each badge variant is reachable', function (): void {
    $driver = seedDriverHistory();

    $checkInStatuses = CheckIn::query()->where('user_id', $driver->id)
        ->pluck('status')->map(fn ($s): string => $s->value)->unique();

    $appointmentStatuses = Appointment::query()->where('user_id', $driver->id)
        ->pluck('status')->map(fn ($s): string => $s->value)->unique();

    expect($checkInStatuses)->toContain('pending', 'completed', 'cancelled')
        ->and($appointmentStatuses)->toContain('scheduled', 'completed', 'cancelled', 'no-show', 'checked-in');
});

it('never leaves a future appointment marked as completed', function (): void {
    $driver = seedDriverHistory();

    $impossible = Appointment::query()
        ->where('user_id', $driver->id)
        ->where('scheduled_for', '>', Date::now())
        ->whereIn('status', ['completed', 'no-show', 'checked-in'])
        ->count();

    expect($impossible)->toBe(0);
});
