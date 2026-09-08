<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PackageType: string implements HasLabel
{
    case Carton = 'carton';
    case Caisse = 'caisse';
    case Palette = 'palette';
    case Conteneur = 'conteneur';
    case Enveloppe = 'enveloppe';
    case Fut = 'fut';

    public function label(): string
    {
        return __('shipment.package_type.'.$this->value);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
