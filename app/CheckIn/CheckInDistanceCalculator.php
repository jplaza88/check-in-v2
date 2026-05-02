<?php

declare(strict_types=1);

namespace App\CheckIn;

use App\DTOs\CheckInDistanceDTO;
use App\Geo\DistanceCalculator;
use App\Models\Location;
use App\Queries\CheckInLocations;
use App\Session\UserSession;
use Illuminate\Support\Collection;

final readonly class CheckInDistanceCalculator
{
    public function __construct(
        private CheckInLocations $locations,
        private DistanceCalculator $distance,
        private UserSession $session,
    ) {}

    /**
     * @return Collection<int, CheckInDistanceDTO>
     */
    public function resolve(float $userLat, float $userLng): Collection
    {
        $cacheKey = sprintf('checkin_distances:%s,%s:%s', $userLat, $userLng, $this->session->getId());
        cache()->forget($cacheKey);

        /** @noinspection PhpPipeOperatorCanBeUsedInspection */
        return cache()->remember($cacheKey, now()->addMinutes(config('app.user_location_distances_ttl')),
            fn (): Collection => $this->calculateDistances($userLat, $userLng)
        );
    }

    /**
     * @return Collection<int, CheckInDistanceDTO>
     */
    private function calculateDistances(float $userLat, float $userLng): Collection
    {
        return $this->locations->execute()
            ->map(fn (Location $location): CheckInDistanceDTO => new CheckInDistanceDTO(
                id: $location->uuid,
                userDistance: $this->distance->calculate(
                    $userLat, $userLng,
                    $location->address->latitude ?? 0.0,
                    $location->address->longitude ?? 0.0,
                )
            ))
            ->sortBy('userDistance')
            ->values();
    }
}
