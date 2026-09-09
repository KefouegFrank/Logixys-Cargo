<?php

namespace Tests\Feature\Http;

use App\Models\MailSuppression;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ResendWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_c2VjcmV0LWtleS1mb3ItdGVzdGluZw==';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.resend.webhook_secret' => self::SECRET]);
    }

    public function test_a_signed_bounce_suppresses_the_address(): void
    {
        $this->send('email.bounced', 'typo@exmaple.com')->assertOk();

        $suppression = MailSuppression::sole();
        $this->assertSame('typo@exmaple.com', $suppression->email);
        $this->assertSame(MailSuppression::REASON_BOUNCED, $suppression->reason);
    }

    public function test_a_signed_complaint_suppresses_the_address(): void
    {
        $this->send('email.complained', 'annoyed@example.com')->assertOk();

        $this->assertSame(MailSuppression::REASON_COMPLAINED, MailSuppression::sole()->reason);
    }

    public function test_a_delivery_event_is_acknowledged_but_suppresses_nothing(): void
    {
        $this->send('email.delivered', 'fine@example.com')->assertOk();

        $this->assertSame(0, MailSuppression::count());
    }

    public function test_an_unsigned_request_is_rejected(): void
    {
        $this->postJson('/webhooks/resend', ['type' => 'email.bounced', 'data' => ['to' => ['x@example.com']]])
            ->assertUnauthorized();

        $this->assertSame(0, MailSuppression::count());
    }

    public function test_a_tampered_payload_is_rejected(): void
    {
        $payload = $this->payload('email.bounced', 'victim@example.com');
        $headers = $this->headers($payload);

        // Same signature, different body.
        $this->call('POST', '/webhooks/resend', [], [], [], $this->server($headers), $this->payload('email.bounced', 'someone-else@example.com'))
            ->assertUnauthorized();

        $this->assertSame(0, MailSuppression::count());
    }

    public function test_a_replayed_request_is_rejected_once_it_is_stale(): void
    {
        $payload = $this->payload('email.bounced', 'old@example.com');

        $this->call('POST', '/webhooks/resend', [], [], [], $this->server($this->headers($payload, time() - 3600)), $payload)
            ->assertUnauthorized();

        $this->assertSame(0, MailSuppression::count());
    }

    public function test_the_endpoint_reports_itself_unconfigured_without_a_secret(): void
    {
        config(['services.resend.webhook_secret' => null]);

        $this->send('email.bounced', 'x@example.com')->assertServiceUnavailable();
    }

    public function test_the_same_address_bouncing_twice_stays_one_row(): void
    {
        $this->send('email.bounced', 'typo@exmaple.com')->assertOk();
        $this->send('email.bounced', 'typo@exmaple.com')->assertOk();

        $this->assertSame(1, MailSuppression::count());
    }

    private function send(string $type, string $email): TestResponse
    {
        $payload = $this->payload($type, $email);

        return $this->call('POST', '/webhooks/resend', [], [], [], $this->server($this->headers($payload)), $payload);
    }

    private function payload(string $type, string $email): string
    {
        return json_encode(['type' => $type, 'data' => ['to' => [$email]]], JSON_THROW_ON_ERROR);
    }

    /** @return array<string, string> */
    private function headers(string $payload, ?int $timestamp = null): array
    {
        $id = 'msg_test';
        $timestamp ??= time();
        $key = base64_decode(substr(self::SECRET, 6), true);
        $signature = base64_encode(hash_hmac('sha256', "{$id}.{$timestamp}.{$payload}", $key, true));

        return [
            'svix-id' => $id,
            'svix-timestamp' => (string) $timestamp,
            'svix-signature' => "v1,{$signature}",
        ];
    }

    /**
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    private function server(array $headers): array
    {
        $server = ['CONTENT_TYPE' => 'application/json'];

        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $server;
    }
}
