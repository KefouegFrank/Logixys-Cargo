<?php

namespace App\Enums;

enum ServiceType: string
{
    case Road = 'road';
    case Air = 'air';
    case Sea = 'sea';
    case Warehousing = 'warehousing';
    case Customs = 'customs';

    // Volumetric weight divisor for chargeable-weight comparisons. Sea/warehousing/customs
    // aren't billed by volumetric weight, so they have none.
    public function volumetricDivisor(): ?int
    {
        return match ($this) {
            self::Air => 6000,
            self::Road => 5000,
            default => null,
        };
    }

    public function label(): string
    {
        return __('shipment.service_type.'.$this->value);
    }
}
