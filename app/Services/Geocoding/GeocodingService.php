<?php

namespace App\Services\Geocoding;

use App\Models\GeocodeCache;

class GeocodingService
{
    public function __construct(private readonly GeocoderProvider $provider) {}

    // Geocodes on write only, never on read: checks the cache first, calls the
    // provider only on a miss, and never caches a failed lookup.
    /** @return array{lat: float, lng: float}|null */
    public function geocode(string $query): ?array
    {
        $normalized = self::normalize($query);

        if ($normalized === '') {
            return null;
        }

        if ($cached = GeocodeCache::where('query_normalized', $normalized)->first()) {
            return ['lat' => (float) $cached->lat, 'lng' => (float) $cached->lng];
        }

        $result = $this->provider->geocode($query);

        if ($result === null) {
            return null;
        }

        GeocodeCache::create([
            'query_normalized' => $normalized,
            'lat' => $result['lat'],
            'lng' => $result['lng'],
            'provider' => $this->provider->name(),
        ]);

        return $result;
    }

    public static function normalize(string $query): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $query)));
    }
}
