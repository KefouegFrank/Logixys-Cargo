<?php

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocale::class,
        ]);

        // Global rather than on the web group: the Filament panel builds its own stack, and
        // Livewire's update endpoint is on neither.
        $middleware->append(SecurityHeaders::class);

        // Rejects any Host header outside APP_URL and its subdomains, so absolute URLs
        // (mail links, redirects) can't be built from a forged Host. No-op in local/testing.
        $middleware->trustHosts();

        // Trusted proxies live in config/trustedproxy.php, which TrustProxies reads itself:
        // config() is not bound yet this early, and trustProxies() would freeze the list at
        // boot. Without them the client address behind the TLS proxy is the proxy's own, so
        // every rate limiter buckets all visitors into one.

        // Signed by Resend rather than by a session.
        $middleware->validateCsrfTokens(except: ['webhooks/resend']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // api/* answers JSON even to a browser; everything else follows the framework
        // default, so a fetch() that asks for JSON gets a 422 rather than a redirect.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
