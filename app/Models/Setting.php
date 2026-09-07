<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Setting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    private const CACHE_KEY = 'settings.all';

    /**
     * Read on every request through the config overlay, so a missing table during
     * migrate//install must degrade to defaults rather than fataling.
     *
     * @return array<string, string>
     */
    public static function values(): array
    {
        try {
            return Cache::rememberForever(
                self::CACHE_KEY,
                fn () => static::query()->pluck('value', 'key')->all(),
            );
        } catch (Throwable) {
            return [];
        }
    }

    /** @param array<string, string|null> $values */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }
}
