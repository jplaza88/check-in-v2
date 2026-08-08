<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\CheckInLocationDTO;
use App\Enums\CheckInErpStatus;
use App\Enums\CheckInStatus;
use App\Enums\RecordHistoryEvent;
use App\Locale\LocaleManager;
use App\Models\CheckIn;
use App\Models\Location;
use App\Models\User;
use App\Queries\CheckInLocation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Random\RandomException;
use RuntimeException;

final readonly class CreateCheckInAction
{
    public function __construct(
        private LocaleManager $localeManager,
        private RecordHistoryAction $history,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(
        array $validated,
        CheckInLocationDTO $locationDTO,
        ?User $user = null,
    ): CheckIn {
        $location = resolve(CheckInLocation::class)->execute($locationDTO->id);

        throw_if(! $location instanceof Location, RuntimeException::class, 'Invalid location');

        $checkIn = CheckIn::query()->create([
            ...Arr::except($validated, ['po_numbers']),
            'uuid' => Str::uuid()->toString(),
            'reference_number' => $this->generateReferenceNumber(),
            'location_id' => $location->id,
            'user_id' => $user?->id,
            'status' => CheckInStatus::Pending,
            'erp_status' => CheckInErpStatus::Pending,
            'locale' => $this->localeManager->getLocale(request()),
        ]);

        // The opening row of the trail an admin reads.
        $this->history->handle(
            record: $checkIn,
            event: RecordHistoryEvent::Created,
            context: ['guest' => ! $user instanceof User],
        );

        return $checkIn;
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
                    |> (fn ($x): array => array_map(fn (): string => $chars[random_int(0, mb_strlen($chars) - 1)], $x))
                    |> (fn ($x): string => implode('', $x));
        } while (CheckIn::query()->where('reference_number', $reference)->exists());

        return $reference;
    }
}
