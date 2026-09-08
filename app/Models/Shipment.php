<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Services\DistanceCalculator;
use App\Services\Geocoding\GeocodingService;
use App\Services\PackageTotalsCalculator;
use App\Services\ShipmentTotalsCalculator;
use App\Services\TrackingNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tracking_number', 'status', 'service_type', 'shipment_mode', 'carrier_name', 'carrier_reference', 'locale',
    'customer_id', 'carrier_id',
    'shipper_name', 'shipper_company', 'shipper_email', 'shipper_phone', 'shipper_address', 'shipper_postcode', 'shipper_city', 'shipper_country',
    'receiver_name', 'receiver_company', 'receiver_email', 'receiver_phone', 'receiver_address', 'receiver_postcode', 'receiver_city', 'receiver_country',
    'origin_label', 'origin_lat', 'origin_lng', 'origin_location_id',
    'destination_label', 'destination_lat', 'destination_lng', 'destination_location_id',
    'pickup_date', 'pickup_time', 'departure_time', 'expected_delivery_date', 'delivered_at',
    'goods_description', 'internal_notes', 'currency',
    'package_count', 'total_quantity', 'total_weight_kg', 'volumetric_weight_kg',
    'chargeable_weight_kg', 'total_volume_cbm', 'declared_value',
    'total_ht', 'tax_amount', 'total_ttc',
    'freight_cost', 'insurance_cost', 'customs_cost', 'other_cost',
    'tax_rate', 'tax_label', 'tax_exemption_note',
    'payment_mode', 'payment_status', 'created_by',
])]
class Shipment extends Model
{
    protected static function booted(): void
    {
        // Derived from the charge fields unless this save set them explicitly — the form
        // sends its own figures so an agent can override what the calculator produced.
        static::saving(function (Shipment $shipment) {
            if ($shipment->isDirty(['total_ht', 'tax_amount', 'total_ttc'])) {
                return;
            }

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

        // Geocode origin/destination on write if coordinates weren't given, then fix
        // distance_km once from the result. Neither is recomputed on later edits.
        static::creating(function (Shipment $shipment) {
            // Both fields are read-only on the form, so they are assigned here rather than
            // submitted — that also covers the seeders and any later API path.
            if (blank($shipment->tracking_number)) {
                $shipment->tracking_number = app(TrackingNumberGenerator::class)->generate();
            }

            if (blank($shipment->status)) {
                $shipment->status = ShipmentStatus::Pending;
            }

            if ($shipment->origin_lat === null && $shipment->origin_lng === null && $shipment->origin_label) {
                $coords = app(GeocodingService::class)->geocode($shipment->origin_label);
                $shipment->origin_lat = $coords['lat'] ?? null;
                $shipment->origin_lng = $coords['lng'] ?? null;
            }

            if ($shipment->destination_lat === null && $shipment->destination_lng === null && $shipment->destination_label) {
                $coords = app(GeocodingService::class)->geocode($shipment->destination_label);
                $shipment->destination_lat = $coords['lat'] ?? null;
                $shipment->destination_lng = $coords['lng'] ?? null;
            }

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
            'volumetric_weight_kg' => 'decimal:2',
            'chargeable_weight_kg' => 'decimal:2',
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

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<Carrier, $this> */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    /** @return BelongsTo<Location, $this> */
    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Expected delivery date has passed with no delivery and no cancellation recorded.
     * Shared by the dashboard's "En retard" stat and its shipments-needing-attention table.
     *
     * @param  Builder<Shipment>  $query
     * @return Builder<Shipment>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereNotIn('status', [ShipmentStatus::Delivered, ShipmentStatus::Cancelled])
            ->whereNotNull('expected_delivery_date')
            ->where('expected_delivery_date', '<', now()->toDateString());
    }

    /** @return HasMany<Package, $this> */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    /**
     * Rewrites the roll-up columns from the package rows. The admin form fills these itself
     * as rows are edited, so this is for callers with no form — seeders and imports.
     */
    public function recalculatePackageAggregates(): void
    {
        $packages = $this->packages()->get();
        $divisor = (int) config('shipping.volumetric_divisor', PackageTotalsCalculator::DEFAULT_DIVISOR)
            ?: PackageTotalsCalculator::DEFAULT_DIVISOR;

        $actualWeight = (float) $packages->sum('weight_kg');
        $volumetricWeight = (float) $packages->sum(fn (Package $package) => $package->totalVolumetricWeightKg($divisor) ?? 0);

        $this->forceFill([
            'package_count' => $packages->count(),
            'total_quantity' => $packages->sum('quantity'),
            'total_weight_kg' => $actualWeight,
            'volumetric_weight_kg' => round($volumetricWeight, 2),
            'chargeable_weight_kg' => round(max($actualWeight, $volumetricWeight), 2),
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
