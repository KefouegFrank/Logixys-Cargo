<?php

namespace App\Enums;

enum PaymentMode: string
{
    case Virement = 'virement';
    case Especes = 'especes';
    case Carte = 'carte';
    case Credit = 'credit';
}
