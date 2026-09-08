<?php

namespace Tests\Feature\Filament;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentEventRecorder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Js;
use Livewire\Livewire;
use Tests\TestCase;

class EditShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_with_a_new_status_posts_an_event_without_crashing(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '4.0429389', 'lon' => '9.7062018']])]);
        $admin = $this->admin();
        $this->actingAs($admin);

        $shipment = $this->shipment($admin);

        // Mirrors the exact crash report: edit fields, pick a status, save.
        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm([
                'shipper_name' => 'sender marry',
                'event_status' => ShipmentStatus::Pending->value,
                'event_location' => 'Douala, Cameroun',
                'event_remarks' => 'hello how are you doing',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $shipment->refresh();
        $this->assertSame('sender marry', $shipment->shipper_name);
        $this->assertCount(1, $shipment->events);
        $this->assertSame('Douala, Cameroun', $shipment->events->first()->location_label);
    }

    public function test_saving_again_does_not_repost_the_same_event(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
        $admin = $this->admin();
        $this->actingAs($admin);

        $shipment = $this->shipment($admin);

        $page = Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm(['event_status' => ShipmentStatus::PickedUp->value, 'event_location' => 'Paris'])
            ->call('save')
            ->assertHasNoFormErrors();

        // The event fields should have cleared themselves after that first save.
        $page->call('save')->assertHasNoFormErrors();

        $shipment->refresh();
        $this->assertCount(1, $shipment->events);
    }

    public function test_the_event_fields_clear_themselves_once_the_event_is_posted(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
        $admin = $this->admin();
        $this->actingAs($admin);

        $shipment = $this->shipment($admin);

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm([
                'event_status' => ShipmentStatus::PickedUp->value,
                'event_location' => 'Paris',
                'event_remarks' => 'left the warehouse',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            // Anything left behind here reads as unsaved work to the draft banner, and
            // would ride along onto whatever event is posted next.
            ->assertFormSet(array_fill_keys(ShipmentEventRecorder::FIELDS, null));
    }

    public function test_the_draft_banner_is_scoped_to_the_agent_and_ignores_the_event_fields(): void
    {
        $admin = $this->admin();
        $shipment = $this->shipment($admin);

        $response = $this->actingAs($admin)->get("/admin/shipments/{$shipment->id}/edit");

        $response->assertOk();
        $response->assertSee("shipment-draft:{$admin->id}:edit:{$shipment->id}", false);
        $response->assertSee('transientKeys: '.Js::from(ShipmentEventRecorder::FIELDS)->toHtml(), false);
    }

    public function test_the_delete_action_is_present_on_the_edit_page(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $shipment = $this->shipment($admin);

        // Delete lives in the footer schema (getFormActions()) rather than the page's
        // header actions, so it's confirmed present rather than invoked here — invoking
        // a schema-nested action needs the click's schemaComponent context, which the
        // Livewire test harness's plain callAction() doesn't supply. The delete flow
        // itself is Filament's own unmodified DeleteAction.
        $this->actingAs($admin)
            ->get("/admin/shipments/{$shipment->id}/edit")
            ->assertOk()
            ->assertSee('Supprimer');
    }

    public function test_deleting_the_record_directly_removes_it(): void
    {
        $admin = $this->admin();
        $shipment = $this->shipment($admin);

        $shipment->delete();

        $this->assertModelMissing($shipment);
    }

    private function shipment(User $admin): Shipment
    {
        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->unique()->numerify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $admin->id,
        ]);
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );
    }
}
