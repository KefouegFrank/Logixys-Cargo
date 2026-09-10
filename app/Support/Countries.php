<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/** ISO country codes paired with names in whichever language is being displayed. */
class Countries
{
    /** @return array<string, string> code => localised name, sorted by name */
    public static function options(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        return Cache::rememberForever("countries.{$locale}", function () use ($locale) {
            $names = [];

            foreach (config('countries', []) as $code) {
                $names[$code] = self::name($code, $locale);
            }

            // Accent-aware ordering, so Égypte lands next to Équateur rather than last.
            collator_asort(collator_create($locale), $names);

            return $names;
        });
    }

    public static function name(?string $code, ?string $locale = null): ?string
    {
        if (blank($code)) {
            return null;
        }

        return \Locale::getDisplayRegion('-'.strtoupper($code), $locale ?? app()->getLocale());
    }
}
