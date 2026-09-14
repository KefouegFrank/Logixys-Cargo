<?php

namespace Tests\Feature\Http;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_a_forwarded_address_from_the_local_proxy_is_believed(): void
    {
        $this->submitContactForm(remoteAddress: '127.0.0.1', forwardedFor: '203.0.113.7');

        $this->assertSame('203.0.113.7', $this->lastRecordedAddress());
    }

    // The other half of the rule: a visitor reaching the app directly cannot claim to be
    // someone else and walk away from a rate limit.
    public function test_a_forwarded_address_from_anywhere_else_is_ignored(): void
    {
        $this->submitContactForm(remoteAddress: '198.51.100.5', forwardedFor: '203.0.113.7');

        $this->assertSame('198.51.100.5', $this->lastRecordedAddress());
    }

    public function test_the_proxy_list_never_trusts_everything(): void
    {
        $this->assertNotContains('*', config('trustedproxy.proxies'));
        $this->assertNotEmpty(config('trustedproxy.proxies'));
    }

    private function submitContactForm(string $remoteAddress, string $forwardedFor): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => $remoteAddress])
            ->withHeaders(['X-Forwarded-For' => $forwardedFor])
            ->post('/fr/contact', [
                'name' => 'Visiteur',
                'email' => 'visiteur@example.test',
                'subject' => 'quote',
                'message' => 'A message long enough to clear validation.',
            ])
            ->assertRedirect();
    }

    private function lastRecordedAddress(): ?string
    {
        return ContactMessage::latest('id')->value('ip_address');
    }
}
