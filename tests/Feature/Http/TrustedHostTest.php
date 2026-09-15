<?php

namespace Tests\Feature\Http;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * TrustHosts rejects a request whose Host header falls outside APP_URL and its
 * subdomains — but only outside local/testing, so the ordinary test client (which always
 * builds absolute URLs rooted at APP_URL) never exercises it. Constructing the request by
 * hand, and forcing the environment, is what it takes to reach the code that runs on the
 * server.
 */
class TrustedHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_forged_host_header_is_rejected_outside_local(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $response = $this->dispatch('http://evil.example.test/fr');

        $this->assertSame(400, $response->getStatusCode());
    }

    public function test_the_real_host_is_still_served_outside_local(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $response = $this->dispatch(config('app.url').'/fr');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_a_subdomain_of_the_app_url_is_still_served(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $host = parse_url(config('app.url'), PHP_URL_HOST);
        $response = $this->dispatch('http://tracking.'.$host.'/fr');

        $this->assertSame(200, $response->getStatusCode());
    }

    private function dispatch(string $absoluteUrl): Response
    {
        $kernel = $this->app->make(Kernel::class);
        $response = $kernel->handle($request = Request::create($absoluteUrl, 'GET'));
        $kernel->terminate($request, $response);

        return $response;
    }
}
