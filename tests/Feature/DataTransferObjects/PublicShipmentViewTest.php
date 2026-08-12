<?php

namespace Tests\Feature\DataTransferObjects;

use App\DataTransferObjects\PublicShipmentView;
use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class PublicShipmentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_masks_shipper_and_receiver_names(): void
    {
        $shipment = $this->shipment(['shipper_name' => 'Jean Martin', 'receiver_name' => 'Alice Dupont']);

        $view = PublicShipmentView::fromModel($shipment);

        $this->assertSame('J. M.', $view->shipperMasked);
        $this->assertSame('A. D.', $view->receiverMasked);
    }

    public function test_masks_a_single_word_name(): void
    {
        $shipment = $this->shipment(['shipper_name' => 'Dupont']);

        $view = PublicShipmentView::fromModel($shipment);

        $this->assertSame('D.', $view->shipperMasked);
    }

    public function test_only_includes_public_events_sorted_by_occurred_at(): void
    {
        $shipment = $this->shipment();
        $agent = User::first();

        $shipment->events()->create([
            'status' => ShipmentStatus::PickedUp, 'occurred_at' => now()->subDays(2),
            'is_public' => true, 'created_by' => $agent->id,
        ]);
        $shipment->events()->create([
            'status' => ShipmentStatus::InTransit, 'occurred_at' => now()->subDay(),
            'is_public' => false, 'created_by' => $agent->id,
        ]);
        $shipment->events()->create([
            'status' => ShipmentStatus::Pending, 'occurred_at' => now()->subDays(3),
            'is_public' => true, 'created_by' => $agent->id,
        ]);

        $view = PublicShipmentView::fromModel($shipment->fresh(['events']));

        $this->assertCount(2, $view->events);
        $this->assertSame(ShipmentStatus::Pending, $view->events[0]->status);
        $this->assertSame(ShipmentStatus::PickedUp, $view->events[1]->status);
    }

    public function test_never_exposes_sensitive_fields(): void
    {
        $forbidden = ['address', 'postcode', 'phone', 'email', 'cost', 'payment', 'remarks', 'declared', 'company'];

        $properties = (new ReflectionClass(PublicShipmentView::class))->getProperties();
        $names = array_map(fn ($p) => strtolower($p->getName()), $properties);

        foreach ($names as $name) {
            foreach ($forbidden as $bad) {
                $this->assertStringNotContainsString($bad, $name, "Property [{$name}] looks like it exposes sensitive data.");
            }
        }
    }

    private function shipment(array $overrides = []): Shipment
    {
        $user = User::firstOrCreate(
            ['email' => 'seed@example.com'],
            ['name' => 'Seed User', 'password' => 'password', 'role' => 'admin'],
        );

        // fresh() re-reads DB-level column defaults (package_count, etc.) that
        // Eloquent doesn't reflect on the in-memory object straight after create().
        return Shipment::create(array_merge([
            'tracking_number' => 'LGXY'.fake()->unique()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris', 'shipper_country' => 'FR',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ], $overrides))->fresh();
    }
}
