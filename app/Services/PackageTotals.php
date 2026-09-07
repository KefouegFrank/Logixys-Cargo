<?php

namespace App\Services;

final class PackageTotals
{
    public function __construct(
        public readonly int $count,
        public readonly int $quantity,
        public readonly float $actualWeightKg,
        public readonly float $volumetricWeightKg,
        public readonly float $chargeableWeightKg,
        public readonly float $volumeCbm,
        public readonly float $declaredValue,
    ) {}
}
