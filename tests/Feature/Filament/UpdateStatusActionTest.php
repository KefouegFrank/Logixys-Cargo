<?php

namespace Tests\Feature\Filament;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Models\Shipment;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class UpdateStatusActionTest extends TestCase
{
    use RefreshDatabase;

    private const LOCATION = 'Lyon, France';

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '45.7640', 'lon' => '4.8357'],
            ]),
        ]);
    }

    public function test_agent_can_update_status_and_it_appends_an_event(): void
    {
        $agent = $this->agent();
        $shipment = $this->shipment($agent);

        $this->actingAs($agent);

        Livewire::test(ListShipments::class)
            ->callAction(TestAction::make('updateStatus')->table($shipment), data: [
                'status' => ShipmentStatus::InTransit->value,
                'location_label' => self::LOCATION,
                'occurred_at' => now(),
                'remarks' => 'Left the depot',
                'is_public' => true,
                'notify_parties' => true,
            ])
            ->assertHasNoFormErrors();

        $shipment->refresh();

        $this->assertSame(ShipmentStatus::InTransit, $shipment->status);
        $this->assertSame(1, $shipment->events()->count());

        $event = $shipment->events()->first();
        $this->assertSame(ShipmentStatus::InTransit, $event->status);
        $this->assertSame(self::LOCATION, $event->location_label);
        $this->assertSame('Left the depot', $event->remarks);
        $this->assertTrue($event->is_public);
        $this->assertSame($agent->id, $event->created_by);
        $this->assertSame('45.7640000', $event->location_lat);
        $this->assertSame('4.8357000', $event->location_lng);
    }

    public function test_the_modal_mounts_successfully_with_the_map_field_present(): void
    {
        $agent = $this->agent();
        $shipment = $this->shipment($agent);

        $this->actingAs($agent);

        // Proves the field's default state resolves inside a real mounted schema
        // without error (LocationPinField can't be constructed standalone -- it needs
        // a live Filament container, which is exactly what mounting here provides).
        Livewire::test(ListShipments::class)
            ->mountAction(TestAction::make('updateStatus')->table($shipment))
            ->assertActionMounted(TestAction::make('updateStatus')->table($shipment));
    }

    public function test_a_manually_dragged_pin_is_saved_as_is_without_geocoding(): void
    {
        $agent = $this->agent();
        $shipment = $this->shipment($agent);

        $this->actingAs($agent);

        Livewire::test(ListShipments::class)
            ->callAction(TestAction::make('updateStatus')->table($shipment), data: [
                'status' => ShipmentStatus::InTransit->value,
                'location_label' => 'Entrepot 3, zone industrielle',
                'location_position' => ['lat' => 47.1234, 'lng' => 7.5678, 'isManual' => true],
                'occurred_at' => now(),
                'is_public' => true,
                'notify_parties' => false,
            ])
            ->assertHasNoFormErrors();

        $event = $shipment->events()->first();

        // The manual position wins even though filling location_label in the same call
        // also fires its live geocode preview (real usage sets these sequentially: type
        // the location, then drag only if the preview is wrong).
        $this->assertSame('47.1234000', $event->location_lat);
        $this->assertSame('7.5678000', $event->location_lng);
        $this->assertTrue($event->is_manual_position);
    }

    public function test_setting_status_to_delivered_stamps_delivered_at(): void
    {
        $agent = $this->agent();
        $shipment = $this->shipment($agent);

        $this->actingAs($agent);

        $this->assertNull($shipment->delivered_at);

        Livewire::test(ListShipments::class)
            ->callAction(TestAction::make('updateStatus')->table($shipment), data: [
                'status' => ShipmentStatus::Delivered->value,
                'location_label' => self::LOCATION,
                'occurred_at' => now(),
                'is_public' => true,
                'notify_parties' => false,
            ]);

        $shipment->refresh();

        $this->assertSame(ShipmentStatus::Delivered, $shipment->status);
        $this->assertNotNull($shipment->delivered_at);
    }

    private function agent(): User
    {
        return User::create([
            'name' => 'Agent', 'email' => 'agent@example.com', 'password' => 'password',
            'role' => UserRole::Agent, 'is_active' => true,
        ]);
    }

    private function shipment(User $creator): Shipment
    {
        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => self::LOCATION, 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => self::LOCATION, 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $creator->id,
        ]);
    }
}
