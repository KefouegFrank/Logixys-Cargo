<?php

namespace App\Services\AddressSearch;

use Illuminate\Support\Facades\Http;

/**
 * Komoot's Photon, worldwide, free and keyless. Unlike Nominatim it is built for
 * search-as-you-type, which Nominatim's usage policy forbids outright.
 */
class PhotonProvider implements AddressSearchProvider
{
    private const ENDPOINT = 'https://photon.komoot.io/api/';

    public function search(string $query, ?string $country = null): array
    {
        $response = Http::timeout(4)->get(self::ENDPOINT, array_filter([
            'q' => $query,
            'limit' => 6,
            'lang' => 'fr',
        ]));

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('features') ?? [])
            ->map(function (array $feature): AddressSuggestion {
                $p = $feature['properties'] ?? [];
                $coordinates = $feature['geometry']['coordinates'] ?? [null, null];

                $line = trim(($p['housenumber'] ?? '').' '.($p['street'] ?? '')) ?: ($p['name'] ?? null);

                return new AddressSuggestion(
                    label: implode(', ', array_filter([
                        $line,
                        trim(($p['postcode'] ?? '').' '.($p['city'] ?? $p['county'] ?? '')) ?: null,
                        $p['country'] ?? null,
                    ])),
                    line: $line,
                    postcode: $p['postcode'] ?? null,
                    city: $p['city'] ?? $p['county'] ?? null,
                    country: isset($p['countrycode']) ? strtoupper($p['countrycode']) : null,
                    lat: isset($coordinates[1]) ? (float) $coordinates[1] : null,
                    lng: isset($coordinates[0]) ? (float) $coordinates[0] : null,
                    source: $this->name(),
                );
            })
            ->filter(fn (AddressSuggestion $suggestion) => $suggestion->label !== '')
            ->values()
            ->all();
    }

    public function name(): string
    {
        return 'photon';
    }

    public function supports(?string $country): bool
    {
        return true;
    }
}
