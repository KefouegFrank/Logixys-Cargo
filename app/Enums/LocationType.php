<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasLabel
{
    case City = 'city';
    case Port = 'port';
    case Airport = 'airport';
    case Warehouse = 'warehouse';
    case Terminal = 'terminal';

    public function label(): string
    {
        return __('shipment.location_type.'.$this->value);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
