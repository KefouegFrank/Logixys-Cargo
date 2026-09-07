<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'company', 'email', 'phone', 'address',
    'postcode', 'city', 'country', 'notes', 'is_active',
])]
class Customer extends Model
{
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
