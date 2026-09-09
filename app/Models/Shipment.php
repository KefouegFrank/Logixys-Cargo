<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Services\DistanceCalculator;
use App\Services\Geocoding\GeocodingService;
use App\Services\PackageTotalsCalculator;
use App\Services\ShipmentNotifier;
use App\Services\ShipmentTotalsCalculator;
use App\Services\TrackingNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;

#[Fillable([
    'tracking_number', 'status', 'service_type', 'shipment_mode', 'shipment_mode_id', 'carrier_name', 'carrier_reference', 'locale',
    'carrier_id',
    'shipper_name', 'shipper_company', 'shipper_email', 'shipper_phone', 'shipper_address', 'shipper_postcode', 'shipper_city', 'shipper_country',
    'receiver_name', 'receiver_company', 'receiver_email', 'receiver_phone', 'receiver_address', 'receiver_postcode', 'receiver_city', 'receiver_country',
    'origin_label', 'origin_lat', 'origin_lng', 'origin_location_id',
    'destination_label', 'destination_lat', 'destination_lng', 'destination_location_id',
    'pickup_date', 'pickup_time', 'departure_time', 'expected_delivery_date', 'delivered_at',
    'goods_description', 'currency',
    'package_count', 'total_quantity', 'total_weight_kg', 'volumetric_weight_kg',
    'chargeable_weight_kg', 'total_volume_cbm', 'declared_value',
    'total_ht', 'tax_amount', 'total_ttc',
    'freight_cost', 'insurance_cost', 'customs_cost', 'other_cost',
    'tax_rate', 'tax_label', 'tax_exemption_note',
    'payment_mode', 'payment_mode_id', 'payment_status', 'created_by',
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

        // Both fields are read-only on the form, so they are assigned here rather than
        // submitted — that also covers the seeders and any later API path.
        static::creating(function (Shipment $shipment) {
            if (blank($shipment->tracking_number)) {
                $shipment->tracking_number = app(TrackingNumberGenerator::class)->generate();
            }

            if (blank($shipment->status)) {
                $shipment->status = ShipmentStatus::Pending;
            }
        });

        // Fills in coordinates the picked location didn't carry, then refreshes distance.
        // Runs on edits too — moving the origin used to leave both stale — but only when
        // the leg actually changed, so an ordinary save never calls the geocoder.
        static::saving(function (Shipment $shipment) {
            foreach (['origin', 'destination'] as $point) {
                if (! $shipment->isDirty("{$point}_label")) {
                    continue;
                }

                if ($shipment->{"{$point}_lat"} !== null || $shipment->{"{$point}_lng"} !== null) {
                    continue;
                }

                if (blank($shipment->{"{$point}_label"})) {
                    continue;
                }

                $coords = app(GeocodingService::class)->geocode($shipment->{"{$point}_label"});
                $shipment->{"{$point}_lat"} = $coords['lat'] ?? null;
                $shipment->{"{$point}_lng"} = $coords['lng'] ?? null;
            }

            if (! $shipment->isDirty(['origin_lat', 'origin_lng', 'destination_lat', 'destination_lng'])) {
                return;
            }

            $shipment->distance_km = ($shipment->origin_lat !== null && $shipment->origin_lng !== null
                && $shipment->destination_lat !== null && $shipment->destination_lng !== null)
                ? app(DistanceCalculator::class)->calculate(
                    (float) $shipment->origin_lat,
                    (float) $shipment->origin_lng,
                    (float) $shipment->destination_lat,
                    (float) $shipment->destination_lng,
                )
                : null;
        });

        // Both parties hear about a shipment appearing and about later edits to it. The
        // notifier merges these with any status change from the same save.
        static::created(fn (Shipment $shipment) => app(ShipmentNotifier::class)->shipmentCreated($shipment));
        static::updated(fn (Shipment $shipment) => app(ShipmentNotifier::class)->shipmentUpdated($shipment));
    }

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'service_type' => ServiceType::class,
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

    /**
     * "75001 Paris, FR" from whichever of those the row actually has. Ville and Pays left
     * the booking form, so older rows carry them and newer ones lean on the address line.
     */
    public function partyLocality(string $prefix): string
    {
        $city = trim($this->{"{$prefix}_postcode"}.' '.$this->{"{$prefix}_city"});

        return trim($city.', '.$this->{"{$prefix}_country"}, ' ,');
    }

    // payment_status is a free-text column on the admin form, so a value with no
    // translation shows exactly as it was typed.
    public function paymentStatusLabel(): string
    {
        $key = 'shipment.payment_status.'.$this->payment_status;

        return Lang::has($key) ? __($key) : (string) $this->payment_status;
    }

    /** @return BelongsTo<PaymentMode, $this> */
    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    /** @return BelongsTo<ShipmentMode, $this> */
    public function shipmentMode(): BelongsTo
    {
        return $this->belongsTo(ShipmentMode::class, 'shipment_mode_id');
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

        $actualWeight = (float) $packages->sum(fn (Package $package) => $package->totalWeightKg());
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

    /** @return HasMany<Receipt, $this> */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
