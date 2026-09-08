<?php

namespace App\DataTransferObjects;

use App\Enums\PackageType;
use App\Models\Package;

/**
 * Dimensions and weight only — no unit_value or amount, the same call as declared_value
 * on the shipment itself: what it's worth stays internal.
 */
final class PublicShipmentPackage
{
    public function __construct(
        public readonly PackageType $packageType,
        public readonly int $quantity,
        public readonly ?string $description,
        public readonly ?float $lengthCm,
        public readonly ?float $widthCm,
        public readonly ?float $heightCm,
        public readonly ?float $weightKg,
    ) {}

    public static function fromModel(Package $package): self
    {
        return new self(
            packageType: $package->package_type,
            quantity: $package->quantity,
            description: $package->description,
            lengthCm: $package->length_cm !== null ? (float) $package->length_cm : null,
            widthCm: $package->width_cm !== null ? (float) $package->width_cm : null,
            heightCm: $package->height_cm !== null ? (float) $package->height_cm : null,
            weightKg: $package->weight_kg !== null ? (float) $package->weight_kg : null,
        );
    }
}
