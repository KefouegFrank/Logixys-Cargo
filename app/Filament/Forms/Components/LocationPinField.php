<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class LocationPinField extends Field
{
    protected string $view = 'filament.forms.components.location-pin-field';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(['lat' => null, 'lng' => null, 'isManual' => false]);
    }

    public function getCenterLat(): float
    {
        return (float) ($this->getState()['lat'] ?? 46.6034);
    }

    public function getCenterLng(): float
    {
        return (float) ($this->getState()['lng'] ?? 2.2137);
    }
}
