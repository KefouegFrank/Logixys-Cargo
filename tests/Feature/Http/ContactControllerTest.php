<?php

namespace Tests\Feature\Http;

use App\Enums\ContactSubject;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageAcknowledged;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_the_form_renders_in_every_locale(): void
    {
        foreach (array_keys(config('locales')) as $locale) {
            $this->get("/{$locale}/contact")
                ->assertOk()
                ->assertSee(__('contact.heading'))
                ->assertSee(__('contact.info_heading'));
        }
    }

    public function test_a_valid_submission_is_stored_and_both_emails_go_out(): void
    {
        $response = $this->post('/fr/contact', $this->payload());

        $response->assertRedirect();
        $response->assertSessionHas('contact.sent', true);

        $message = ContactMessage::sole();
        $this->assertSame('Claire Dubois', $message->name);
        $this->assertSame('claire@example.com', $message->email);
        $this->assertSame(ContactSubject::Quote, $message->subject);
        $this->assertSame('fr', $message->locale);
        $this->assertNull($message->read_at);

        Mail::assertQueued(ContactMessageReceived::class, fn ($mail) => $mail->hasTo(config('brand.contact.email')));
        Mail::assertQueued(ContactMessageAcknowledged::class, fn ($mail) => $mail->hasTo('claire@example.com'));
    }

    public function test_the_acknowledgement_is_written_in_the_visitors_language(): void
    {
        $this->post('/de/contact', $this->payload());

        $this->assertSame('de', ContactMessage::sole()->locale);
        Mail::assertQueued(ContactMessageAcknowledged::class, fn ($mail) => $mail->locale === 'de');
    }

    public function test_the_internal_copy_replies_to_the_visitor(): void
    {
        $this->post('/fr/contact', $this->payload());

        Mail::assertQueued(
            ContactMessageReceived::class,
            fn (ContactMessageReceived $mail) => $mail->envelope()->replyTo[0]->address === 'claire@example.com',
        );
    }

    #[DataProvider('invalidPayloads')]
    /** @param array<string, mixed> $overrides */
    public function test_invalid_input_is_rejected(array $overrides, string $field): void
    {
        $response = $this->from('/fr/contact')->post('/fr/contact', [...$this->payload(), ...$overrides]);

        $response->assertRedirect('/fr/contact');
        $response->assertSessionHasErrors($field);

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingQueued();
    }

    /** @return array<string, array{0: array<string, mixed>, 1: string}> */
    public static function invalidPayloads(): array
    {
        return [
            'no name' => [['name' => ''], 'name'],
            'no email' => [['email' => ''], 'email'],
            'malformed email' => [['email' => 'claire@@example'], 'email'],
            'unknown subject' => [['subject' => 'sponsorship'], 'subject'],
            'message too short' => [['message' => 'devis ?'], 'message'],
            'message too long' => [['message' => str_repeat('a', 5001)], 'message'],
            'letters in the phone' => [['phone' => 'call me maybe'], 'phone'],
        ];
    }

    public function test_a_filled_honeypot_is_rejected_without_storing_anything(): void
    {
        $response = $this->from('/fr/contact')->post('/fr/contact', [
            ...$this->payload(),
            ContactRequest::HONEYPOT => 'http://spam.example',
        ]);

        $response->assertSessionHasErrors(ContactRequest::HONEYPOT);

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingQueued();
    }

    public function test_the_honeypot_never_reaches_the_database(): void
    {
        $this->post('/fr/contact', $this->payload());

        $this->assertArrayNotHasKey(ContactRequest::HONEYPOT, ContactMessage::sole()->getAttributes());
    }

    public function test_the_visitors_address_is_recorded_for_triage(): void
    {
        $this->post('/fr/contact', $this->payload(), ['HTTP_USER_AGENT' => 'PHPUnit browser']);

        $message = ContactMessage::sole();
        $this->assertNotNull($message->ip_address);
        $this->assertSame('PHPUnit browser', $message->user_agent);
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/fr/contact', $this->payload())->assertRedirect();
        }

        $this->post('/fr/contact', $this->payload())->assertStatus(429);

        $this->assertSame(5, ContactMessage::count());
    }

    /** @return array<string, string> */
    private function payload(): array
    {
        return [
            'name' => 'Claire Dubois',
            'email' => 'claire@example.com',
            'phone' => '+33 6 12 34 56 78',
            'subject' => ContactSubject::Quote->value,
            'message' => 'Bonjour, je souhaite un devis pour deux palettes vers Douala.',
        ];
    }
}
