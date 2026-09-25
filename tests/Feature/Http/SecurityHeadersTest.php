<?php

namespace Tests\Feature\Http;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function publicPages(): array
    {
        return [
            'home' => ['/fr'],
            'tracking form' => ['/fr/suivi'],
            'contact' => ['/fr/contact'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_pages_refuse_framing_and_send_origin_only_referrer(string $url): void
    {
        $response = $this->get($url)->assertOk();

        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
    }

    // The panel builds its own middleware stack, so it is the one most likely to be missed.
    public function test_the_panel_refuses_framing(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]));

        $response = $this->get('/admin/shipments')->assertOk();

        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
    }

    // The tracking number is in the URL; OSM tiles must get a Referer, but only the origin.
    public function test_a_tracking_result_page_sends_origin_only_referrer(): void
    {
        $response = $this->get('/fr/suivi/LGXY123456789-CARGO');

        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
    }

    public function test_hsts_is_sent_over_https_only(): void
    {
        $this->assertNull($this->get('/fr')->headers->get('Strict-Transport-Security'));

        $secure = $this->get('https://localhost/fr');

        $this->assertStringContainsString('max-age=', (string) $secure->headers->get('Strict-Transport-Security'));
    }
}
