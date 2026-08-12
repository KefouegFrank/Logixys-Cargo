<?php

namespace App\Services;

use App\Models\Shipment;
use RuntimeException;

class TrackingNumberGenerator
{
    private const PREFIX = 'LGXY';

    // Crockford base32: 0-9 and A-Z minus I, L, O, U (ambiguous when read aloud).
    private const ALPHABET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    private const SUFFIX_LENGTH = 9;

    private const MAX_ATTEMPTS = 10;

    public function generate(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = self::PREFIX.$this->randomSuffix();

            if (! Shipment::where('tracking_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException('Could not generate a unique tracking number after '.self::MAX_ATTEMPTS.' attempts.');
    }

    public static function normalize(string $input): string
    {
        return strtoupper(str_replace([' ', '-'], '', $input));
    }

    public static function matchesFormat(string $normalized): bool
    {
        return (bool) preg_match('/^'.self::PREFIX.'['.self::ALPHABET.']{'.self::SUFFIX_LENGTH.'}$/', $normalized);
    }

    protected function randomSuffix(): string
    {
        $suffix = '';
        $max = strlen(self::ALPHABET) - 1;

        for ($i = 0; $i < self::SUFFIX_LENGTH; $i++) {
            $suffix .= self::ALPHABET[random_int(0, $max)];
        }

        return $suffix;
    }
}
