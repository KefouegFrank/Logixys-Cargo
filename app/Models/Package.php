<?php

namespace App\Models;

use App\Enums\PackageType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shipment_id', 'quantity', 'package_type', 'description',
    'weight_kg', 'length_cm', 'width_cm', 'height_cm', 'unit_value', 'amount',
])]
class Package extends Model
{
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
}
