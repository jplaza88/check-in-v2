<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\AppointmentLocationDTO;
use App\Enums\AppointmentStatus;
use App\Locale\LocaleManager;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\User;
use App\Queries\AppointmentLocation;
use http\Exception\RuntimeException;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreateAppointmentAction
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
        $location = resolve(AppointmentLocation::class)->execute($locationDTO->id, true);

        throw_if(! $location instanceof Location, RuntimeException::class, 'Invalid location');

        return Appointment::query()->create([
            'uuid' => Str::uuid()->toString(),
            'location_id' => $location->id,
            'user_id' => $user?->id,
            'scheduled_for' => Date::parse($validated['datetime'], $location->timezone),
            'drivers_name' => $validated['drivers_name'],
            'drivers_cellphone' => $validated['drivers_cellphone'],
            'locale' => $this->localeManager->getLocale(request()),
            'status' => AppointmentStatus::Scheduled,
        ]);
    }
}
