<?php

namespace App\Services\AddressSearch;

/** One pickable address, in the shape the booking form fills its fields from. */
final class AddressSuggestion
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $line,
        public readonly ?string $postcode,
        public readonly ?string $city,
        public readonly ?string $country,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly string $source,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'line' => $this->line,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country' => $this->country,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'source' => $this->source,
        ];
    }
}
