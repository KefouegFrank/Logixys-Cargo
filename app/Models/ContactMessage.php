<?php

namespace App\Models;

use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'phone', 'service_type', 'subject', 'message', 'locale', 'is_handled', 'handled_by', 'consent_at'])]
class ContactMessage extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'service_type' => ServiceType::class,
            'is_handled' => 'boolean',
            'consent_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
