<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Virement, espèces, and whatever else the office adds. A managed list rather than an
 * enum so a new payment method can be created from the booking form and stay.
 */
#[Fillable(['name', 'is_active'])]
class PaymentMode extends Model
{
    /** The rows the migration seeds. The office is free to add to them. */
    public const VIREMENT = 'Virement bancaire';

    public const ESPECES = 'Espèces';

    public const CARTE = 'Carte bancaire';

    public const CREDIT = 'Compte crédit';

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
