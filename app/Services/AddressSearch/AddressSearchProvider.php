<?php

namespace App\Services\AddressSearch;

interface AddressSearchProvider
{
    /**
     * Suggestions for a partial address, best first.
     *
     * @param  string|null  $country  ISO-3166-1 alpha-2 hint, when the caller knows one.
     * @return array<int, AddressSuggestion>
     */
    public function search(string $query, ?string $country = null): array;

    public function name(): string;

    /** False when the provider has no key, or cannot serve this country at all. */
    public function supports(?string $country): bool;
}
