<?php

namespace Tests\Feature\Console;

use App\Enums\ContactSubject;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneContactMessageTriageDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_message_past_the_window_has_its_triage_data_cleared(): void
    {
        config(['contact.triage_retention_days' => 30]);
        $old = $this->message(createdAt: now()->subDays(31));

        $this->artisan('contact-messages:prune')->assertSuccessful();

        $old->refresh();
        $this->assertNull($old->ip_address);
        $this->assertNull($old->user_agent);
    }

    public function test_a_message_still_inside_the_window_is_left_alone(): void
    {
        config(['contact.triage_retention_days' => 30]);
        $recent = $this->message(createdAt: now()->subDays(29));

        $this->artisan('contact-messages:prune');

        $recent->refresh();
        $this->assertNotNull($recent->ip_address);
        $this->assertNotNull($recent->user_agent);
    }

    // Content stays, only the two triage columns move — the message is still a business
    // record after the run.
    public function test_the_message_itself_is_left_intact(): void
    {
        $old = $this->message(createdAt: now()->subDays(60));

        $this->artisan('contact-messages:prune');

        $old->refresh();
        $this->assertSame('Client audit', $old->name);
        $this->assertSame('client@example.test', $old->email);
        $this->assertSame('Une question sur mon envoi.', $old->message);
    }

    public function test_running_twice_is_harmless(): void
    {
        $old = $this->message(createdAt: now()->subDays(60));

        $this->artisan('contact-messages:prune');
        $this->artisan('contact-messages:prune')->assertSuccessful();

        $this->assertNull($old->refresh()->ip_address);
    }

    private function message(\DateTimeInterface $createdAt): ContactMessage
    {
        $message = ContactMessage::create([
            'name' => 'Client audit',
            'email' => 'client@example.test',
            'subject' => ContactSubject::Quote,
            'message' => 'Une question sur mon envoi.',
            'locale' => 'fr',
            'ip_address' => '203.0.113.9',
            'user_agent' => 'Mozilla/5.0 (Audit)',
        ]);

        $message->forceFill(['created_at' => $createdAt])->save();

        return $message;
    }
}
