<?php

namespace App\Services;

class DistanceCalculator
{
    private const EARTH_RADIUS_KM = 6371;

    public function calculate(float $originLat, float $originLng, float $destinationLat, float $destinationLng): int
    {
        $lat1 = deg2rad($originLat);
        $lat2 = deg2rad($destinationLat);
        $deltaLat = deg2rad($destinationLat - $originLat);
        $deltaLng = deg2rad($destinationLng - $originLng);

        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round(self::EARTH_RADIUS_KM * $c);
    }
}
