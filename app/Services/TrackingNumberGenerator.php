<?php

namespace App\Services;

use App\Models\Shipment;
use RuntimeException;

class TrackingNumberGenerator
{
    private const PREFIX = 'LGXY';

    private const DIGITS = 9;

    private const SUFFIX = '-CARGO';

    private const MAX_ATTEMPTS = 10;

    public function generate(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = self::PREFIX.$this->randomDigits().self::SUFFIX;

            if (! Shipment::where('tracking_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Could not generate a unique tracking number after '.self::MAX_ATTEMPTS.' attempts.');
    }

    /**
     * Rebuilds the canonical stored form from whatever a visitor pastes into the
     * tracking box — spacing, casing, a missing hyphen or a missing -CARGO suffix.
     */
    public static function normalize(string $input): string
    {
        $bare = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $input) ?? '');

        if (str_ends_with($bare, 'CARGO')) {
            $bare = substr($bare, 0, -5);
        }

        return $bare.self::SUFFIX;
    }

    public static function matchesFormat(string $normalized): bool
    {
        return (bool) preg_match('/^'.self::PREFIX.'\d{'.self::DIGITS.'}'.self::SUFFIX.'$/', $normalized);
    }

    protected function randomDigits(): string
    {
        return str_pad((string) random_int(0, 10 ** self::DIGITS - 1), self::DIGITS, '0', STR_PAD_LEFT);
    }
}
