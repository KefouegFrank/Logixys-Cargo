<?php

namespace App\Services\AddressSearch;

use Illuminate\Support\Facades\Http;

/**
 * Base Adresse Nationale — the French government's address file. Free, no key, and
 * far better than OSM inside France, down to the house number.
 */
class BanProvider implements AddressSearchProvider
{
    private const ENDPOINT = 'https://api-adresse.data.gouv.fr/search/';

    /**
     * BAN answers every query with its nearest French guess, so "bonaberi douala" comes
     * back as a village in Brittany at 0.32. Genuine matches, part-typed ones included,
     * sit above 0.8; this drops the noise so worldwide results are not buried under it.
     */
    private const MIN_SCORE = 0.75;

    public function search(string $query, ?string $country = null): array
    {
        $response = Http::timeout(3)->get(self::ENDPOINT, [
            'q' => $query,
            'limit' => 6,
            'autocomplete' => 1,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('features') ?? [])
            ->map(function (array $feature): ?AddressSuggestion {
                $properties = $feature['properties'] ?? [];
                $coordinates = $feature['geometry']['coordinates'] ?? [null, null];

                if (($properties['score'] ?? 0) < self::MIN_SCORE) {
                    return null;
                }

                return new AddressSuggestion(
                    label: $properties['label'] ?? '',
                    line: trim(($properties['housenumber'] ?? '').' '.($properties['street'] ?? $properties['name'] ?? '')) ?: null,
                    postcode: $properties['postcode'] ?? null,
                    city: $properties['city'] ?? null,
                    country: 'FR',
                    lat: isset($coordinates[1]) ? (float) $coordinates[1] : null,
                    lng: isset($coordinates[0]) ? (float) $coordinates[0] : null,
                    source: $this->name(),
                );
            })
            ->filter(fn (?AddressSuggestion $suggestion) => $suggestion !== null && $suggestion->label !== '')
            ->values()
            ->all();
    }

    public function name(): string
    {
        return 'ban';
    }

    // France only, so a query already known to be elsewhere skips the round trip.
    public function supports(?string $country): bool
    {
        return $country === null || strtoupper($country) === 'FR';
    }
}
