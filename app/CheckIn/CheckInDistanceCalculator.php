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
        // Key on exact coordinates + session so the cache self-invalidates
        // when the driver moves; the TTL only bounds reuse of an unchanged result.
        $cacheKey = sprintf('checkin_distances:%s,%s:%s', $userLat, $userLng, $this->session->getId());

        $cached = cache()->get($cacheKey);

        if ($cached instanceof Collection
            && $cached->every(static fn (mixed $distance): bool => $distance instanceof CheckInDistanceDTO)) {
            return $cached;
        }

        $distances = $this->calculateDistances($userLat, $userLng);

        cache()->put($cacheKey, $distances, now()->addMinutes(config('app.user_location_distances_ttl')));

        return $distances;
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
