<?php

namespace App\Services\Geocoding;

interface GeocoderProvider
{
    /** @return array{lat: float, lng: float}|null */
    public function geocode(string $query): ?array;

    public function name(): string;
}
