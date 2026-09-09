<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Addresses Resend told us not to write to again. Shipment parties are typed in by hand,
 * so a typo would otherwise bounce on every status change and cost us domain reputation.
 */
#[Fillable(['email', 'reason', 'detail', 'suppressed_at'])]
class MailSuppression extends Model
{
    public const REASON_BOUNCED = 'bounced';

    public const REASON_COMPLAINED = 'complained';

    protected function casts(): array
    {
        return ['suppressed_at' => 'datetime'];
    }

    public static function normalize(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    public static function suppresses(?string $email): bool
    {
        if (blank($email)) {
            return true;
        }

        return static::query()->where('email', static::normalize($email))->exists();
    }

    public static function record(string $email, string $reason, ?string $detail = null): void
    {
        static::query()->updateOrCreate(
            ['email' => static::normalize($email)],
            ['reason' => $reason, 'detail' => $detail, 'suppressed_at' => now()],
        );
    }
}
