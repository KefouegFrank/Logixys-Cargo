<?php

namespace App\Services;

use App\Models\Shipment;
use RuntimeException;

class TrackingNumberGenerator
{
    private const PREFIX = 'LGXY';

    private const LENGTH = 9;

    private const SUFFIX = '-CARGO';

    private const MAX_ATTEMPTS = 10;

    /**
     * Crockford base32: digits plus A-Z minus I, L, O and U, which get misread against
     * 1, 1, 0 and V when read aloud or handwritten. 32^9 candidates, against 10^9 for
     * digits alone — the difference between a guessable number and one that isn't.
     */
    private const ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    public function generate(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = self::PREFIX.$this->randomCode().self::SUFFIX;

            if (! Shipment::where('tracking_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Could not generate a unique tracking number after '.self::MAX_ATTEMPTS.' attempts.');
    }

    /**
     * Rebuilds the canonical stored form from whatever a visitor pastes into the
     * tracking box — spacing, casing, a missing hyphen or a missing -CARGO suffix. Safe
     * against a false match on the serial itself: O never appears in it, so "CARGO" can
     * only ever be the literal suffix.
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
        return (bool) preg_match(self::pattern(), $normalized);
    }

    /**
     * The same shape as a delimited regex, for the admin form field — an agent can edit
     * the auto-generated number, but not save one the public tracking page would reject.
     */
    public static function formatRegex(): string
    {
        return self::pattern();
    }

    private static function pattern(): string
    {
        return '/^'.self::PREFIX.'['.self::ALPHABET.']{'.self::LENGTH.'}'.self::SUFFIX.'$/';
    }

    protected function randomCode(): string
    {
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return $code;
    }
}
