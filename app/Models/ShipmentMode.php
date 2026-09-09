<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Porte à porte, Port à port, and whatever else the office adds. A managed list rather
 * than an enum so a new mode can be created from the booking form and stay.
 */
#[Fillable(['name', 'is_active'])]
class ShipmentMode extends Model
{
    /** The rows the migration seeds. The office is free to add to them. */
    public const DOOR_TO_DOOR = 'Porte à porte';

    public const DOOR_TO_PORT = 'Porte à port';

    public const PORT_TO_PORT = 'Port à port';

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** @return HasMany<Shipment, $this> */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
