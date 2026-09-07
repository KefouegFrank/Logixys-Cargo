<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceType: string implements HasLabel
{
    case Road = 'road';
    case Air = 'air';
    case Sea = 'sea';
    case Warehousing = 'warehousing';
    case Customs = 'customs';

    public function label(): string
    {
        return __('shipment.service_type.'.$this->value);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
