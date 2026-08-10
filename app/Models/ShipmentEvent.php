<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use App\Services\Geocoding\GeocodingService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shipment_id', 'status', 'location_label', 'location_lat', 'location_lng',
    'is_manual_position', 'occurred_at', 'remarks', 'is_public', 'created_by',
])]
class ShipmentEvent extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'location_lat' => 'decimal:7',
            'location_lng' => 'decimal:7',
            'is_manual_position' => 'boolean',
            'is_public' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Append-only: a mistake is corrected by inserting a new event, not by editing history.
        static::updating(fn () => throw new \RuntimeException('Shipment events are append-only and cannot be updated.'));
        static::deleting(fn () => throw new \RuntimeException('Shipment events are append-only and cannot be deleted.'));

        // Geocode on write, never on read. A manually dragged pin always wins.
        static::creating(function (ShipmentEvent $event) {
            if ($event->location_label && $event->location_lat === null && $event->location_lng === null && ! $event->is_manual_position) {
                $coords = app(GeocodingService::class)->geocode($event->location_label);

                if ($coords !== null) {
                    $event->location_lat = $coords['lat'];
                    $event->location_lng = $coords['lng'];
                }
            }
        });
    }

    /** @return BelongsTo<Shipment, $this> */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
