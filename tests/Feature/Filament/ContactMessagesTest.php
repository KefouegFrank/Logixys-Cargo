<?php

namespace Tests\Feature\Filament;

use App\Enums\ContactSubject;
use App\Enums\UserRole;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContactMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_agent_sees_the_messages_in_the_list(): void
    {
        $this->actingAs($this->agent());
        $message = $this->message();

        Livewire::test(ListContactMessages::class)
            ->assertCanSeeTableRecords([$message])
            ->assertSee('Claire Dubois');
    }

    public function test_opening_a_message_marks_it_read(): void
    {
        $this->actingAs($this->agent());
        $message = $this->message();

        $this->assertNull($message->read_at);

        Livewire::test(ViewContactMessage::class, ['record' => $message->id])->assertOk();

        $this->assertNotNull($message->refresh()->read_at);
    }

    public function test_the_navigation_badge_counts_only_unread_messages(): void
    {
        $this->actingAs($this->agent());

        $this->message();
        $this->message()->markAsRead();

        $this->assertSame('1', ContactMessageResource::getNavigationBadge());
    }

    public function test_messages_cannot_be_created_from_the_admin(): void
    {
        $this->assertFalse(ContactMessageResource::canCreate());
    }

    public function test_the_unread_filter_hides_messages_already_opened(): void
    {
        $this->actingAs($this->agent());

        $unread = $this->message();
        $read = $this->message();
        $read->markAsRead();

        Livewire::test(ListContactMessages::class)
            ->filterTable('unread')
            ->assertCanSeeTableRecords([$unread])
            ->assertCanNotSeeTableRecords([$read]);
    }

    private function message(): ContactMessage
    {
        return ContactMessage::create([
            'name' => 'Claire Dubois',
            'email' => 'claire@example.com',
            'phone' => '+33 6 12 34 56 78',
            'subject' => ContactSubject::Quote,
            'message' => 'Bonjour, je souhaite un devis pour deux palettes vers Douala.',
            'locale' => 'fr',
        ]);
    }

    private function agent(): User
    {
        return User::firstOrCreate(
            ['email' => 'agent@example.com'],
            ['name' => 'Agent', 'password' => 'password', 'role' => UserRole::Agent, 'is_active' => true],
        );
    }
}
