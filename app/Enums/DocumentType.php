<?php

namespace App\Enums;

enum DocumentType: string
{
    case Pod = 'pod';
    case Receipt = 'receipt';
    case Customs = 'customs';
    case Photo = 'photo';
    case Other = 'other';
}
