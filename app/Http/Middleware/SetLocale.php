<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        abort_unless(in_array($locale, self::supported(), true), 404);

        App::setLocale($locale);

        return $next($request);
    }

    /** @return array<int, string> */
    public static function supported(): array
    {
        return array_keys(config('locales'));
    }

    // Feeds the {locale} route constraint, so adding a locale is a config change only.
    public static function routePattern(): string
    {
        return implode('|', self::supported());
    }
}
