<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shipment_id', 'number', 'issued_at', 'total_ht', 'tax_amount', 'total_ttc', 'currency', 'path', 'created_by'])]
class Receipt extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'total_ht' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_ttc' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // Numbers are sequential with no gaps, and a reissued document must be
        // byte-identical to what the customer already holds (doc section 6).
        static::updating(fn () => throw new \RuntimeException('Receipts are immutable once issued.'));
        static::deleting(fn () => throw new \RuntimeException('Receipts are immutable once issued.'));
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
