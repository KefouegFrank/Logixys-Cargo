<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response headers for every route, panel included. Deliberately narrow: framing and
 * referrer leakage are the two real exposures here, and a full content policy would have
 * to allow the inline script Livewire and Alpine both rely on, which buys little.
 */
class SecurityHeaders
{
    /** One year. Only sent over HTTPS, since a browser ignores it otherwise. */
    private const HSTS_MAX_AGE = 31536000;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // The panel is worth framing: a clickjacked admin can be walked through a delete.
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'none'");

        // The tracking number is the credential and it sits in the URL, so the map's tile
        // requests would otherwise hand it to OpenStreetMap in the Referer.
        $response->headers->set('Referrer-Policy', 'no-referrer');

        $response->headers->set('X-Content-Type-Options', 'nosniff');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age='.self::HSTS_MAX_AGE);
        }

        return $response;
    }
}
