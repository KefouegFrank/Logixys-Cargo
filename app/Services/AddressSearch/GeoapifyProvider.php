<?php

namespace App\Services\AddressSearch;

use Illuminate\Support\Facades\Http;

/** Fallback for when the keyless pair returns nothing. 3,000 lookups a day on the free tier. */
class GeoapifyProvider implements AddressSearchProvider
{
    private const ENDPOINT = 'https://api.geoapify.com/v1/geocode/autocomplete';

    public function __construct(private readonly ?string $apiKey) {}

    public function search(string $query, ?string $country = null): array
    {
        $response = Http::timeout(6)->get(self::ENDPOINT, array_filter([
            'text' => $query,
            'limit' => 6,
            'format' => 'geojson',
            'filter' => $country ? 'countrycode:'.strtolower($country) : null,
            'apiKey' => $this->apiKey,
        ]));

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('features') ?? [])
            ->map(function (array $feature): AddressSuggestion {
                $p = $feature['properties'] ?? [];

                return new AddressSuggestion(
                    label: $p['formatted'] ?? '',
                    line: $p['address_line1'] ?? trim(($p['housenumber'] ?? '').' '.($p['street'] ?? '')) ?: null,
                    postcode: $p['postcode'] ?? null,
                    city: $p['city'] ?? null,
                    country: isset($p['country_code']) ? strtoupper($p['country_code']) : null,
                    lat: isset($p['lat']) ? (float) $p['lat'] : null,
                    lng: isset($p['lon']) ? (float) $p['lon'] : null,
                    source: $this->name(),
                );
            })
            ->filter(fn (AddressSuggestion $suggestion) => $suggestion->label !== '')
            ->values()
            ->all();
    }

    public function name(): string
    {
        return 'geoapify';
    }

    public function supports(?string $country): bool
    {
        return filled($this->apiKey);
    }
}
