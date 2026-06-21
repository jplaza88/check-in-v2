<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\AppointmentLocationDTO;
use App\Enums\AppointmentStatus;
use App\Locale\LocaleManager;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final readonly class StoreAppointmentAction
{
    public function __construct(private LocaleManager $localeManager) {}

    /**
     * @param  array<string, mixed>  $validated
     *
     * @throws Throwable
     */
    public function handle(
        array $validated,
        AppointmentLocationDTO $locationDTO,
        #[CurrentUser] ?User $user = null,
    ): Appointment {
        return DB::transaction(function () use ($validated, $locationDTO, $user): Appointment {
            $location = Location::query()->where('uuid', $locationDTO->id)->firstOrFail();

            $appointment = Appointment::query()->create([
                'uuid' => Str::uuid()->toString(),
                'location_id' => $location->id,
                'user_id' => $user?->id,
                'scheduled_for' => $validated['datetime'],
                'drivers_name' => $validated['drivers_name'],
                'drivers_cellphone' => $validated['drivers_cellphone'],
                'locale' => $this->localeManager->getLocale(request()),
                'status' => AppointmentStatus::Scheduled,
            ]);

            $poNumbers = $validated['po_numbers'];

            $appointment->purchaseOrders()->createMany(
                array_map(fn (string $number): array => ['number' => $number], $poNumbers)
            );

            return $appointment;
        });
    }
}
