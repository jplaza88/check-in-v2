<?php

declare(strict_types=1);

use App\Enums\HistoryPeriod;
use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use App\Queries\DriverAppointments;
use App\Queries\DriverCheckIns;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Date;

function driverForHistoryList(string $email = 'driver@example.com'): User
{
    return User::query()->create([
        'name' => 'John Driver',
        'email' => $email,
        'password' => 'secret-password',
    ]);
}

/**
 * @return array<int, CheckIn>
 */
function checkInsFor(User $user, ?HistoryPeriod $period = null): array
{
    return resolve(DriverCheckIns::class)
        ->execute($user, ($period ?? HistoryPeriod::AllTime)->since())
        ->items();
}

/**
 * @return array<int, Appointment>
 */
function appointmentsFor(User $user, ?HistoryPeriod $period = null): array
{
    return resolve(DriverAppointments::class)
        ->execute($user, ($period ?? HistoryPeriod::AllTime)->since())
        ->items();
}

it('returns only the given driver check-ins', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();
    $other = driverForHistoryList('other@example.com');

    CheckIn::factory()->forUser($driver)->atLocation($location)->create();
    CheckIn::factory()->forUser($other)->atLocation($location)->create();
    CheckIn::factory()->atLocation($location)->create();

    $results = checkInsFor($driver);

    expect($results)->toHaveCount(1)
        ->and($results[0]->user_id)->toBe($driver->id);
});

it('excludes soft deleted check-ins', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();

    CheckIn::factory()->forUser($driver)->atLocation($location)->create();
    CheckIn::factory()->forUser($driver)->atLocation($location)->create()->delete();

    expect(checkInsFor($driver))->toHaveCount(1);
});

it('orders check-ins newest first', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();

    $oldest = CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDays(10))->create();
    $newest = CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDay())->create();
    $middle = CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDays(5))->create();

    expect(collect(checkInsFor($driver))->pluck('id')->all())
        ->toBe([$newest->id, $middle->id, $oldest->id]);
});

it('bounds check-ins by the selected period', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();

    CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDays(10))->create();
    CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDays(60))->create();
    CheckIn::factory()->forUser($driver)->atLocation($location)
        ->createdAt(Date::now()->subDays(400))->create();

    expect(checkInsFor($driver, HistoryPeriod::ThirtyDays))->toHaveCount(1)
        ->and(checkInsFor($driver, HistoryPeriod::NinetyDays))->toHaveCount(2)
        ->and(checkInsFor($driver, HistoryPeriod::TwelveMonths))->toHaveCount(2)
        ->and(checkInsFor($driver, HistoryPeriod::AllTime))->toHaveCount(3);
});

it('paginates check-ins without overlap between pages', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();

    foreach (range(1, 20) as $daysAgo) {
        CheckIn::factory()->forUser($driver)->atLocation($location)
            ->createdAt(Date::now()->subDays($daysAgo))->create();
    }

    $first = resolve(DriverCheckIns::class)->execute($driver, null, 15);

    expect($first->items())->toHaveCount(15)
        ->and($first->hasMorePages())->toBeTrue();

    Paginator::currentPageResolver(fn (): int => 2);
    $second = resolve(DriverCheckIns::class)->execute($driver, null, 15);
    Paginator::currentPageResolver(fn (): int => 1);

    $firstIds = collect($first->items())->pluck('id');
    $secondIds = collect($second->items())->pluck('id');

    expect($second->items())->toHaveCount(5)
        ->and($second->hasMorePages())->toBeFalse()
        ->and($secondIds->intersect($firstIds))->toBeEmpty();
});

it('returns only the given driver appointments, newest slot first', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();
    $other = driverForHistoryList('other@example.com');

    $older = Appointment::factory()->forUser($driver)->atLocation($location)
        ->scheduledFor(Date::now()->subDays(9))->create();
    $newer = Appointment::factory()->forUser($driver)->atLocation($location)
        ->scheduledFor(Date::now()->subDays(2))->create();
    Appointment::factory()->forUser($other)->atLocation($location)->create();

    expect(collect(appointmentsFor($driver))->pluck('id')->all())
        ->toBe([$newer->id, $older->id]);
});

it('keeps future appointments visible under every period preset', function (): void {
    $location = Location::factory()->create();
    $driver = driverForHistoryList();

    $future = Appointment::factory()->forUser($driver)->atLocation($location)
        ->scheduledFor(Date::now()->addDays(60))->create();
    Appointment::factory()->forUser($driver)->atLocation($location)
        ->scheduledFor(Date::now()->subDays(200))->create();

    $thirtyDays = collect(appointmentsFor($driver, HistoryPeriod::ThirtyDays));

    expect($thirtyDays)->toHaveCount(1)
        ->and($thirtyDays->first()->id)->toBe($future->id)
        ->and(appointmentsFor($driver, HistoryPeriod::AllTime))->toHaveCount(2);
});
