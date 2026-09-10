<?php

namespace App\Services\AddressSearch;

use Illuminate\Support\Facades\Http;

/** Last resort in the chain. 5,000 lookups a day on the free tier, 2 per second. */
class LocationIqProvider implements AddressSearchProvider
{
    private const ENDPOINT = 'https://api.locationiq.com/v1/autocomplete';

    public function __construct(private readonly ?string $apiKey) {}

    public function search(string $query, ?string $country = null): array
    {
        $response = Http::timeout(6)->get(self::ENDPOINT, array_filter([
            'key' => $this->apiKey,
            'q' => $query,
            'limit' => 6,
            'normalizecity' => 1,
            'countrycodes' => $country ? strtolower($country) : null,
        ]));

        // A no-match is a 404 here rather than an empty list, so it is not an error.
        if ($response->failed()) {
            return [];
        }

        return collect($response->json() ?? [])
            ->map(function (array $result): AddressSuggestion {
                $address = $result['address'] ?? [];

                return new AddressSuggestion(
                    label: $result['display_name'] ?? '',
                    line: trim(($address['house_number'] ?? '').' '.($address['road'] ?? $address['name'] ?? '')) ?: null,
                    postcode: $address['postcode'] ?? null,
                    city: $address['city'] ?? null,
                    country: isset($address['country_code']) ? strtoupper($address['country_code']) : null,
                    lat: isset($result['lat']) ? (float) $result['lat'] : null,
                    lng: isset($result['lon']) ? (float) $result['lon'] : null,
                    source: $this->name(),
                );
            })
            ->filter(fn (AddressSuggestion $suggestion) => $suggestion->label !== '')
            ->values()
            ->all();
    }

    public function name(): string
    {
        return 'locationiq';
    }

    public function supports(?string $country): bool
    {
        return filled($this->apiKey);
    }
}
