<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class LocationPinField extends Field
{
    protected string $view = 'filament.forms.components.location-pin-field';

    /** @var array<string, string> */
    protected array $statusLabels = [];

    protected ?string $locationField = null;

    protected ?string $statusField = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(['lat' => null, 'lng' => null, 'isManual' => false]);
    }

    /**
     * Names two sibling fields whose values fill the marker's popup.
     *
     * @param  array<string, string>  $statusLabels  status value => human label
     */
    public function popupFrom(string $locationField, string $statusField, array $statusLabels = []): static
    {
        $this->locationField = $locationField;
        $this->statusField = $statusField;
        $this->statusLabels = $statusLabels;

        return $this;
    }

    public function getLocationStatePath(): ?string
    {
        return $this->locationField ? $this->siblingStatePath($this->locationField) : null;
    }

    public function getStatusStatePath(): ?string
    {
        return $this->statusField ? $this->siblingStatePath($this->statusField) : null;
    }

    /** @return array<string, string> */
    public function getStatusLabels(): array
    {
        return $this->statusLabels;
    }

    public function getCenterLat(): float
    {
        // A field with no matching model attribute can hydrate as null on edit
        // rather than the default array — hence the (array) cast before indexing.
        return (float) (((array) $this->getState())['lat'] ?? 46.6034);
    }

    public function getCenterLng(): float
    {
        return (float) (((array) $this->getState())['lng'] ?? 2.2137);
    }

    // Siblings live under the same container as this field.
    private function siblingStatePath(string $name): string
    {
        $container = $this->getStatePath();
        $container = str_contains($container, '.') ? substr($container, 0, strrpos($container, '.')) : '';

        return $container === '' ? $name : "{$container}.{$name}";
    }
}
