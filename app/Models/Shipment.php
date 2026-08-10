<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Services\DistanceCalculator;
use App\Services\ShipmentTotalsCalculator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tracking_number', 'status', 'service_type', 'shipment_mode', 'carrier_name', 'carrier_reference', 'locale',
    'shipper_name', 'shipper_company', 'shipper_email', 'shipper_phone', 'shipper_address', 'shipper_postcode', 'shipper_city', 'shipper_country',
    'receiver_name', 'receiver_company', 'receiver_email', 'receiver_phone', 'receiver_address', 'receiver_postcode', 'receiver_city', 'receiver_country',
    'origin_label', 'origin_lat', 'origin_lng', 'destination_label', 'destination_lat', 'destination_lng',
    'pickup_date', 'expected_delivery_date', 'delivered_at',
    'goods_description', 'currency',
    'freight_cost', 'insurance_cost', 'customs_cost', 'other_cost',
    'tax_rate', 'tax_label', 'tax_exemption_note',
    'payment_mode', 'payment_status', 'created_by',
])]
class Shipment extends Model
{
    // total_ht/tax_amount/total_ttc are derived and recomputed on every save; never set directly.
    protected static function booted(): void
    {
        static::saving(function (Shipment $shipment) {
            $totals = app(ShipmentTotalsCalculator::class)->calculate(
                (float) $shipment->freight_cost,
                (float) $shipment->insurance_cost,
                (float) $shipment->customs_cost,
                (float) $shipment->other_cost,
                (float) $shipment->tax_rate,
            );

            $shipment->total_ht = $totals->totalHt;
            $shipment->tax_amount = $totals->taxAmount;
            $shipment->total_ttc = $totals->totalTtc;
        });

        // distance_km is fixed at creation from origin/destination coordinates, not recomputed on edit.
        static::creating(function (Shipment $shipment) {
            if ($shipment->origin_lat !== null && $shipment->origin_lng !== null
                && $shipment->destination_lat !== null && $shipment->destination_lng !== null) {
                $shipment->distance_km = app(DistanceCalculator::class)->calculate(
                    (float) $shipment->origin_lat,
                    (float) $shipment->origin_lng,
                    (float) $shipment->destination_lat,
                    (float) $shipment->destination_lng,
                );
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'service_type' => ServiceType::class,
            'shipment_mode' => ShipmentMode::class,
            'origin_lat' => 'decimal:7',
            'origin_lng' => 'decimal:7',
            'destination_lat' => 'decimal:7',
            'destination_lng' => 'decimal:7',
            'pickup_date' => 'date',
            'expected_delivery_date' => 'date',
            'delivered_at' => 'datetime',
            'total_weight_kg' => 'decimal:2',
            'total_volume_cbm' => 'decimal:3',
            'declared_value' => 'decimal:2',
            'freight_cost' => 'decimal:2',
            'insurance_cost' => 'decimal:2',
            'customs_cost' => 'decimal:2',
            'other_cost' => 'decimal:2',
            'total_ht' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_ttc' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<Package, $this> */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    // package_count/total_weight_kg/total_volume_cbm/declared_value are derived from package
    // rows; called by Package's model events whenever a row is added, changed, or removed.
    public function recalculatePackageAggregates(): void
    {
        $packages = $this->packages()->get();

        $this->forceFill([
            'package_count' => $packages->count(),
            'total_weight_kg' => $packages->sum('weight_kg'),
            'total_volume_cbm' => $packages->sum(fn (Package $package) => $package->totalVolumeM3() ?? 0),
            'declared_value' => $packages->sum('amount'),
        ])->save();
    }

    /** @return HasMany<ShipmentEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    /** @return HasMany<Document, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** @return HasMany<Receipt, $this> */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
