<?php

namespace App\Models;

use App\Enums\PackageType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shipment_id', 'quantity', 'package_type', 'description',
    'weight_kg', 'length_cm', 'width_cm', 'height_cm', 'unit_value',
])]
class Package extends Model
{
    protected static function booted(): void
    {
        // amount is derived from quantity x unit_value; never set directly.
        static::saving(function (Package $package) {
            $package->amount = round((float) $package->unit_value * $package->quantity, 2);
        });

        static::saved(fn (Package $package) => $package->shipment->recalculatePackageAggregates());
        static::deleted(fn (Package $package) => $package->shipment->recalculatePackageAggregates());
    }

    protected function casts(): array
    {
        return [
            'package_type' => PackageType::class,
            'weight_kg' => 'decimal:2',
            'length_cm' => 'decimal:2',
            'width_cm' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'unit_value' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Shipment, $this> */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    // Per-unit volume in m3, from cm dimensions. Null if any dimension is missing.
    public function volumeM3(): ?float
    {
        if ($this->length_cm === null || $this->width_cm === null || $this->height_cm === null) {
            return null;
        }

        return ((float) $this->length_cm * (float) $this->width_cm * (float) $this->height_cm) / 1_000_000;
    }

    public function totalVolumeM3(): ?float
    {
        return $this->volumeM3() === null ? null : $this->volumeM3() * $this->quantity;
    }

    // (L x W x H) / divisor, the standard freight volumetric-weight formula. Null without
    // dimensions or a divisor for this service type.
    public function totalVolumetricWeightKg(?int $divisor): ?float
    {
        if ($divisor === null || $this->length_cm === null || $this->width_cm === null || $this->height_cm === null) {
            return null;
        }

        $perUnit = ((float) $this->length_cm * (float) $this->width_cm * (float) $this->height_cm) / $divisor;

        return $perUnit * $this->quantity;
    }

    public function chargeableWeightKg(?int $divisor): float
    {
        return max((float) $this->weight_kg, $this->totalVolumetricWeightKg($divisor) ?? 0.0);
    }
}
