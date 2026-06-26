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
use Random\RandomException;
use Throwable;

final readonly class CreateAppointmentAction
{
    public function __construct(private LocaleManager $localeManager) {}

    /**
     * @param  array<string, mixed>  $validated
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
            'reference_number' => $this->generateReferenceNumber(),
            'location_id' => $location->id,
            'user_id' => $user?->id,
            'scheduled_for' => Date::parse($validated['datetime'], $location->timezone),
            'drivers_name' => $validated['drivers_name'],
            'drivers_cellphone' => $validated['drivers_cellphone'],
            'locale' => $this->localeManager->getLocale(request()),
            'status' => AppointmentStatus::Scheduled,
        ]);
    }

    /**
     * @throws RandomException
     */
    private function generateReferenceNumber(): string
    {
        // Excludes ambiguous characters (0/O, 1/I/L) to make references easy to read and type.
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $reference = range(1, 8)
                    |> (fn($x) => array_map(fn(): string => $chars[random_int(0, mb_strlen($chars) - 1)], $x,))
                    |> (fn($x) => implode('', $x));
        } while (Appointment::query()->where('reference_number', $reference)->exists());

        return $reference;
    }
}
