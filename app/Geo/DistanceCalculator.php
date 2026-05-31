<?php

declare(strict_types=1);

namespace App\Geo;

final class DistanceCalculator
{
    private const float EARTH_RADIUS_MILES = 3958.8;

    /**
     * Haversine distance formula
     */
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2, int $decimalPlaces = 1): float
    {
        /*
         * $earthRadius = 3958.8; // miles
         * $earthRadius = 6371; //km
        */

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * asin(sqrt($a));

        return round(self::EARTH_RADIUS_MILES * $c, $decimalPlaces);
    }
}
