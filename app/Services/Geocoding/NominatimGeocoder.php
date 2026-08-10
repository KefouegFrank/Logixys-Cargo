<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Http;

class NominatimGeocoder implements GeocoderProvider
{
    private const ENDPOINT = 'https://nominatim.openstreetmap.org/search';

    public function __construct(private readonly string $userAgent) {}

    public function geocode(string $query): ?array
    {
        // Usage policy requires a real User-Agent and caps requests at 1/sec;
        // geocode-on-write plus the cache in GeocodingService keeps us well under that.
        $response = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->get(self::ENDPOINT, [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

        if ($response->failed()) {
            return null;
        }

        $result = $response->json(0);

        if ($result === null) {
            return null;
        }

        return [
            'lat' => (float) $result['lat'],
            'lng' => (float) $result['lon'],
        ];
    }

    public function name(): string
    {
        return 'nominatim';
    }
}
