<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Factories\AppointmentFactory;
use Database\Factories\CheckInFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Random\RandomException;

final class DriverHistorySeeder extends Seeder
{
    /**
     * Buckets of [daysAgoFrom, daysAgoTo, checkIns, appointments], spread so the
     * 30d / 90d / 12m / all-time presets each return a visibly different set and
     * the list needs more than one page. Negative days are in the future.
     *
     * @var list<array{int, int, int, int}>
     */
    private const array BUCKETS = [
        [-30, -1, 0, 3],
        [1, 7, 5, 2],
        [8, 30, 8, 4],
        [31, 90, 10, 6],
        [91, 365, 12, 7],
        [366, 900, 8, 5],
    ];

    public function run(): void
    {
        $driver = User::query()->where('email', UserSeeder::ADMIN_EMAIL)->first();

        if (! $driver instanceof User) {
            $this->command?->warn('DriverHistorySeeder skipped: run UserSeeder first.');

            return;
        }

        if (CheckIn::query()->where('user_id', $driver->id)->exists()) {
            $this->command?->info('DriverHistorySeeder skipped: this driver already has history.');

            return;
        }

        $locations = Location::query()->where('is_active', true)->get();

        if ($locations->isEmpty()) {
            $this->command?->warn('DriverHistorySeeder skipped: run LocationSeeder first.');

            return;
        }

        foreach (self::BUCKETS as [$from, $to, $checkIns, $appointments]) {
            for ($i = 1; $i <= $checkIns; $i++) {
                $this->seedCheckIn($driver, $locations, $this->momentBetween($from, $to), $i);
            }

            for ($i = 1; $i <= $appointments; $i++) {
                $this->seedAppointment($driver, $locations, $this->momentBetween($from, $to), $i);
            }
        }
    }

    /**
     * @param  Collection<int, Location>  $locations
     */
    private function seedCheckIn(User $driver, Collection $locations, CarbonImmutable $when, int $index): void
    {
        $factory = CheckIn::factory()
            ->forUser($driver)
            ->atLocation($locations->random())
            ->createdAt($when);

        $this->attachPurchaseOrders($this->withCheckInStatus($factory, $index)->create());
    }

    /**
     * @param  Collection<int, Location>  $locations
     *
     * @throws RandomException
     */
    private function seedAppointment(User $driver, Collection $locations, CarbonImmutable $when, int $index): void
    {
        $slot = $when->setTime(random_int(7, 16), [0, 15, 30, 45][random_int(0, 3)]);

        $factory = Appointment::factory()
            ->forUser($driver)
            ->atLocation($locations->random())
            ->scheduledFor($slot);

        // A slot that has not happened yet can only be scheduled or cancelled.
        $factory = $slot->isFuture()
            ? $factory->scheduled()
            : $this->withAppointmentStatus($factory, $index);

        $this->attachPurchaseOrders($factory->create());
    }

    /**
     * Mostly completed, with enough of the other statuses that every badge
     * variant is visible on the page.
     */
    private function withCheckInStatus(CheckInFactory $factory, int $index): CheckInFactory
    {
        return match ($index % 5) {
            0 => $factory->cancelled(),
            1 => $factory->pending(),
            default => $factory->completed(),
        };
    }

    private function withAppointmentStatus(AppointmentFactory $factory, int $index): AppointmentFactory
    {
        return match ($index % 6) {
            0 => $factory->noShow(),
            1 => $factory->cancelled('Truck breakdown en route'),
            2 => $factory->checkedIn(),
            default => $factory->completed(),
        };
    }

    private function attachPurchaseOrders(CheckIn|Appointment $record): void
    {
        // Sequential suffix rather than a second random draw, so the
        // (purchasable, number) unique index can never collide within a record.
        $base = random_int(10000, 99900);

        for ($i = 0; $i < random_int(1, 3); $i++) {
            $record->purchaseOrders()->save(new PurchaseOrder([
                'number' => sprintf('PO-%05d', $base + $i),
            ]));
        }
    }

    private function momentBetween(int $daysAgoFrom, int $daysAgoTo): CarbonImmutable
    {
        return Date::now()
            ->subDays(random_int(min($daysAgoFrom, $daysAgoTo), max($daysAgoFrom, $daysAgoTo)))
            ->setTime(random_int(6, 20), random_int(0, 59));
    }
}
