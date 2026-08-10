<?php

namespace Tests\Feature\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShipmentEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_is_geocoded_from_the_label_when_coordinates_are_omitted(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '47.5596', 'lon' => '7.5886'],
            ]),
        ]);

        $event = $this->shipment()->events()->create([
            'status' => ShipmentStatus::InTransit,
            'location_label' => 'Bale, Suisse',
            'occurred_at' => now(),
            'created_by' => $this->creator->id,
        ]);

        $this->assertSame('47.5596000', $event->location_lat);
        $this->assertSame('7.5886000', $event->location_lng);
    }

    public function test_a_manually_positioned_pin_is_never_geocoded(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '0', 'lon' => '0']])]);

        $event = $this->shipment()->events()->create([
            'status' => ShipmentStatus::InTransit,
            'location_label' => 'Entrepot 3, zone industrielle',
            'location_lat' => 47.1, 'location_lng' => 7.2,
            'is_manual_position' => true,
            'occurred_at' => now(),
            'created_by' => $this->creator->id,
        ]);

        Http::assertNothingSent();
        $this->assertSame('47.1000000', $event->location_lat);
        $this->assertSame('7.2000000', $event->location_lng);
    }

    private User $creator;

    private function shipment(): Shipment
    {
        $this->creator = User::create([
            'name' => 'Agent', 'email' => 'agent@example.com', 'password' => 'password', 'role' => 'agent',
        ]);

        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $this->creator->id,
        ]);
    }
}
