<?php

namespace App\Enums;

enum ServiceType: string
{
    case Road = 'road';
    case Air = 'air';
    case Sea = 'sea';
    case Warehousing = 'warehousing';
    case Customs = 'customs';
}
