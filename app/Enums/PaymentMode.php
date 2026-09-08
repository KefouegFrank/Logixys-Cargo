<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMode: string implements HasLabel
{
    case Virement = 'virement';
    case Especes = 'especes';
    case Carte = 'carte';
    case Credit = 'credit';

    public function label(): string
    {
        return __('shipment.payment_mode.'.$this->value);
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
