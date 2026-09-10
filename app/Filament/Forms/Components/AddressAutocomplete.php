<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Concerns\CanBeLengthConstrained;
use Filament\Forms\Components\Concerns\HasPlaceholder;
use Filament\Forms\Components\Field;

/**
 * Text input that offers real addresses as you type and, on a pick, fills the sibling
 * fields named in fills(). The lookup runs server-side; see AddressSearchController.
 */
class AddressAutocomplete extends Field
{
    use CanBeLengthConstrained;
    use HasPlaceholder;

    protected string $view = 'filament.forms.components.address-autocomplete';

    /** @var array<string, string> suggestion key => sibling field name */
    protected array $fills = [];

    protected ?string $countryHint = null;

    /**
     * @param  array<string, string>  $map  e.g. ['postcode' => 'shipper_postcode']
     */
    public function fills(array $map): static
    {
        $this->fills = $map;

        return $this;
    }

    /** Restricts results to one country, when the field only ever means one place. */
    public function country(?string $code): static
    {
        $this->countryHint = $code;

        return $this;
    }

    /** @return array<string, string> Sibling paths, resolved against this field's container. */
    public function getFillPaths(): array
    {
        $container = $this->getStatePath();
        $container = str_contains($container, '.') ? substr($container, 0, strrpos($container, '.')) : '';

        return collect($this->fills)
            ->map(fn (string $name) => $container === '' ? $name : "{$container}.{$name}")
            ->all();
    }

    public function getCountryHint(): ?string
    {
        return $this->countryHint;
    }
}
