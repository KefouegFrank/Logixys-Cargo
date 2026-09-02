<?php

namespace App\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

final class LocaleSwitcher
{
    /**
     * Every supported locale pointing at the equivalent of the current page.
     *
     * @return array<int, array{code: string, native: string, short: string, flag: string, url: string, active: bool}>
     */
    public static function options(): array
    {
        $current = App::currentLocale();

        return array_map(
            fn (string $code, array $meta) => [
                'code' => $code,
                'native' => $meta['native'],
                'short' => $meta['short'],
                'flag' => $meta['flag'],
                'url' => self::urlFor($code),
                'active' => $code === $current,
            ],
            array_keys(config('locales')),
            config('locales'),
        );
    }

    public static function current(): array
    {
        $code = App::currentLocale();

        return ['code' => $code] + config("locales.{$code}", ['native' => $code, 'short' => mb_strtoupper($code), 'flag' => '']);
    }

    /**
     * Keeps the visitor on the same page when they switch language. Route
     * parameters are reused, so /fr/suivi/LGXY-1 maps to /en/suivi/LGXY-1.
     */
    private static function urlFor(string $code): string
    {
        $route = Route::current();

        if ($route === null || $route->getName() === null) {
            return url($code);
        }

        // Only real URI segments: Route::view also exposes view/status/headers as
        // parameters, and those would otherwise be appended as a query string.
        $parameters = array_intersect_key(
            $route->parameters(),
            array_flip($route->parameterNames()),
        );

        return route($route->getName(), array_merge($parameters, ['locale' => $code]));
    }
}
