<?php

namespace App\Models;

use App\Enums\ContactSubject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'email', 'phone', 'subject', 'message', 'locale', 'ip_address', 'user_agent',
])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'subject' => ContactSubject::class,
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * @param  Builder<ContactMessage>  $query
     * @return Builder<ContactMessage>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * ip_address and user_agent exist only for near-term abuse triage; past that they are
     * just an address book of who contacted the company. Leaves the message itself intact.
     *
     * @param  Builder<ContactMessage>  $query
     * @return Builder<ContactMessage>
     */
    public function scopeCarryingTriageDataOlderThan(Builder $query, \DateTimeInterface $cutoff): Builder
    {
        return $query
            ->where('created_at', '<', $cutoff)
            ->where(fn (Builder $q) => $q->whereNotNull('ip_address')->orWhereNotNull('user_agent'));
    }
}
