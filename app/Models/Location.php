<?php

namespace App\Models;

use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type', 'city', 'country', 'lat', 'lng', 'is_active'])]
class Location extends Model
{
    protected function casts(): array
    {
        return [
            'type' => LocationType::class,
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    // What gets copied onto a shipment's origin_label / destination_label.
    public function label(): string
    {
        return collect([$this->name, $this->city, $this->country])
            ->filter()
            ->unique()
            ->implode(', ');
    }
}
