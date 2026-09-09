<?php

namespace Tests\Feature\Notifications;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Mail\ShipmentNotice;
use App\Mail\ShipmentNoticeBrief;
use App\Models\MailSuppression;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentEventRecorder;
use App\Services\ShipmentNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShipmentNotifierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
    }

    public function test_creating_a_shipment_notifies_both_parties_and_the_office(): void
    {
        $this->makeShipment();
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->hasTo('sender@example.com') && $mail->party === 'shipper' && $mail->kind === ShipmentNotifier::CREATED);
        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->hasTo('receiver@example.com') && $mail->party === 'receiver' && $mail->kind === ShipmentNotifier::CREATED);
        Mail::assertQueued(ShipmentNoticeBrief::class, fn ($mail) => $mail->hasTo(config('brand.contact.email')));
        Mail::assertQueuedCount(3);
    }

    public function test_editing_a_shipment_notifies_both_parties_and_the_office(): void
    {
        $shipment = $this->shipment();

        $shipment->update(['goods_description' => 'Pièces détachées']);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->kind === ShipmentNotifier::UPDATED);
        Mail::assertQueuedCount(3);
    }

    public function test_a_status_change_notifies_both_parties_and_the_office(): void
    {
        $shipment = $this->shipment();

        $this->postEvent($shipment, ShipmentStatus::InTransit);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->hasTo('sender@example.com') && $mail->kind === ShipmentNotifier::STATUS);
        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->hasTo('receiver@example.com') && $mail->kind === ShipmentNotifier::STATUS);
        Mail::assertQueuedCount(3);
    }

    #[DataProvider('everyStatus')]
    public function test_every_status_notifies(ShipmentStatus $status): void
    {
        $this->postEvent($this->shipment(), $status);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, 2);
        Mail::assertQueued(ShipmentNoticeBrief::class, 1);
    }

    /** @return array<string, array{0: ShipmentStatus}> */
    public static function everyStatus(): array
    {
        return collect(ShipmentStatus::cases())
            ->mapWithKeys(fn (ShipmentStatus $status) => [$status->value => [$status]])
            ->all();
    }

    public function test_one_save_that_edits_and_posts_a_status_sends_a_single_notice(): void
    {
        $shipment = $this->shipment();

        // What the admin form does: field changes saved, then the event recorded.
        $shipment->update(['goods_description' => 'Pièces détachées']);
        $this->postEvent($shipment, ShipmentStatus::PickedUp);
        $this->flush();

        // The status notice outranks the edit; the customer hears about it once.
        Mail::assertQueued(ShipmentNotice::class, 2);
        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->kind === ShipmentNotifier::STATUS);
        Mail::assertQueuedCount(3);
    }

    public function test_a_status_only_change_raises_no_separate_edit_notice(): void
    {
        $shipment = $this->shipment();

        // The recorder writes status and delivered_at; neither is worth its own mail.
        $this->postEvent($shipment, ShipmentStatus::Delivered);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->kind === ShipmentNotifier::STATUS);
        Mail::assertQueuedCount(3);
    }

    public function test_a_purely_derived_change_notifies_nobody(): void
    {
        $shipment = $this->shipment();

        $shipment->update(['distance_km' => 4321]);
        $this->flush();

        Mail::assertNothingQueued();
    }

    public function test_customer_mail_uses_the_language_recorded_on_the_shipment(): void
    {
        $this->postEvent($this->shipment(['locale' => 'es']), ShipmentStatus::Delivered);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->locale === 'es');
        // The office reads the panel's language whatever the customer speaks.
        Mail::assertQueued(ShipmentNoticeBrief::class, fn ($mail) => $mail->locale === config('app.locale'));
    }

    public function test_a_missing_party_address_is_skipped_without_stopping_the_other(): void
    {
        $this->postEvent($this->shipment(['receiver_email' => null]), ShipmentStatus::PickedUp);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, 1);
        Mail::assertQueued(ShipmentNotice::class, fn ($mail) => $mail->hasTo('sender@example.com'));
    }

    public function test_a_bounced_address_is_suppressed_and_reported_in_the_brief(): void
    {
        MailSuppression::record('receiver@example.com', MailSuppression::REASON_BOUNCED);

        $this->postEvent($this->shipment(), ShipmentStatus::InTransit);
        $this->flush();

        Mail::assertQueued(ShipmentNotice::class, 1);
        Mail::assertQueued(
            ShipmentNoticeBrief::class,
            fn (ShipmentNoticeBrief $mail) => $mail->notified === ['sender@example.com']
                && $mail->skipped === ['receiver@example.com'],
        );
    }

    public function test_the_event_is_stamped_once_a_customer_was_reached(): void
    {
        $shipment = $this->shipment();

        $this->postEvent($shipment, ShipmentStatus::InTransit);
        $this->flush();

        $this->assertNotNull($shipment->events()->sole()->notified_at);
    }

    public function test_nothing_is_stamped_when_no_customer_could_be_reached(): void
    {
        $shipment = $this->shipment(['shipper_email' => null, 'receiver_email' => null]);

        $this->postEvent($shipment, ShipmentStatus::InTransit);
        $this->flush();

        $this->assertNull($shipment->events()->sole()->notified_at);
        // The office still hears about it — a shipment nobody can be told about matters.
        Mail::assertQueued(ShipmentNoticeBrief::class, fn ($mail) => $mail->notified === []);
    }

    public function test_saving_without_a_status_records_no_event(): void
    {
        $shipment = $this->shipment();

        $this->postEvent($shipment, null);
        $this->flush();

        $this->assertSame(0, $shipment->events()->count());
        Mail::assertNothingQueued();
    }

    private function flush(): void
    {
        app(ShipmentNotifier::class)->flush();
    }

    /** @param array<string, mixed> $extra */
    private function postEvent(Shipment $shipment, ?ShipmentStatus $status, array $extra = []): void
    {
        app(ShipmentEventRecorder::class)->record($shipment, [
            'event_status' => $status?->value,
            'event_location' => 'Paris CDG',
            'event_date' => now()->toDateString(),
            'event_time' => now()->format('H:i'),
            ...$extra,
        ]);
    }

    /** Creates a shipment and clears the notice that creating it raises. */
    private function shipment(array $overrides = []): Shipment
    {
        $shipment = $this->makeShipment($overrides);

        $this->flush();
        Mail::fake();

        return $shipment;
    }

    /** @param array<string, mixed> $overrides */
    private function makeShipment(array $overrides = []): Shipment
    {
        $user = User::firstOrCreate(
            ['email' => 'seed@example.com'],
            ['name' => 'Seed User', 'password' => 'password', 'role' => 'admin'],
        );

        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->unique()->numerify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'locale' => 'fr',
            'shipper_name' => 'Jean Martin', 'shipper_city' => 'Paris', 'shipper_email' => 'sender@example.com',
            'receiver_name' => 'Ana Silva', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'receiver_email' => 'receiver@example.com',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
            ...$overrides,
        ]);
    }
}
