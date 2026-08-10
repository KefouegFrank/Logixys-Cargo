<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['query_normalized', 'lat', 'lng', 'provider'])]
class GeocodeCache extends Model
{
    protected $table = 'geocode_cache';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
        ];
    }
}
