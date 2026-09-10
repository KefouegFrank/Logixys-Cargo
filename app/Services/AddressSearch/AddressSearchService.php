<?php

namespace App\Services\AddressSearch;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Walks the configured providers until one answers, so the keyless pair carries the load
 * and the metered fallbacks only run when they find nothing.
 */
class AddressSearchService
{
    /** Below this a query matches half the country and the answer is useless. */
    public const MIN_QUERY_LENGTH = 3;

    /** Autocomplete fires per keystroke; the same prefix shouldn't leave the app twice. */
    private const CACHE_MINUTES = 60;

    /** Enough to fill the dropdown without scrolling it. */
    private const MAX_RESULTS = 8;

    /**
     * @param  array<int, AddressSearchProvider>  $primary  Keyless; all are asked and merged.
     * @param  array<int, AddressSearchProvider>  $fallback  Metered; first one to answer wins.
     */
    public function __construct(
        private readonly array $primary,
        private readonly array $fallback = [],
    ) {}

    /** @return array<int, AddressSuggestion> */
    public function search(string $query, ?string $country = null): array
    {
        $query = trim(preg_replace('/\s+/', ' ', $query));

        if (mb_strlen($query) < self::MIN_QUERY_LENGTH) {
            return [];
        }

        // v2: the cache holds plain arrays now. The version keeps an older shape left in
        // a shared store from being read back as the wrong thing after a deploy.
        $key = 'address-search:v2:'.md5(mb_strtolower($query).'|'.strtolower((string) $country));

        $cached = Cache::remember($key, now()->addMinutes(self::CACHE_MINUTES), function () use ($query, $country) {
            $merged = [];

            foreach ($this->primary as $provider) {
                $merged = [...$merged, ...$this->ask($provider, $query, $country)];
            }

            if ($merged === []) {
                foreach ($this->fallback as $provider) {
                    $merged = $this->ask($provider, $query, $country);

                    if ($merged !== []) {
                        break;
                    }
                }
            }

            return array_map(fn (AddressSuggestion $s) => $s->toArray(), $this->dedupe($merged));
        });

        return array_map(fn (array $row) => new AddressSuggestion(
            label: $row['label'],
            line: $row['line'],
            postcode: $row['postcode'],
            city: $row['city'],
            country: $row['country'],
            lat: $row['lat'],
            lng: $row['lng'],
            source: $row['source'],
        ), $cached);
    }

    /**
     * @return array<int, AddressSuggestion>
     */
    private function ask(AddressSearchProvider $provider, string $query, ?string $country): array
    {
        if (! $provider->supports($country)) {
            return [];
        }

        try {
            return $provider->search($query, $country);
        } catch (Throwable $exception) {
            // One provider being down is the reason there is more than one.
            Log::warning('Address search provider failed.', [
                'provider' => $provider->name(),
                'exception' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Two providers routinely return the same place. Coordinates rounded to about a
     * hundred metres are what decides that, since the labels never match exactly.
     *
     * @param  array<int, AddressSuggestion>  $suggestions
     * @return array<int, AddressSuggestion>
     */
    private function dedupe(array $suggestions): array
    {
        $seen = [];
        $unique = [];

        foreach ($suggestions as $suggestion) {
            // Either an identical label or near-identical coordinates makes it a repeat:
            // one provider often lists the same street twice a few metres apart.
            $keys = [mb_strtolower($suggestion->label)];

            if ($suggestion->lat !== null && $suggestion->lng !== null) {
                $keys[] = round($suggestion->lat, 3).','.round($suggestion->lng, 3);
            }

            if (array_intersect_key($seen, array_flip($keys)) !== []) {
                continue;
            }

            foreach ($keys as $seenKey) {
                $seen[$seenKey] = true;
            }
            $unique[] = $suggestion;

            if (count($unique) >= self::MAX_RESULTS) {
                break;
            }
        }

        return $unique;
    }
}
