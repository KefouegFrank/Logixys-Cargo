<?php

namespace App\Services;

final class ShipmentTotals
{
    public function __construct(
        public readonly float $totalHt,
        public readonly float $taxAmount,
        public readonly float $totalTtc,
    ) {}
}
