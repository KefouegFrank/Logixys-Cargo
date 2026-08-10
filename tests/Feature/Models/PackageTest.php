<?php

namespace Tests\Feature\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_amount_is_computed_from_quantity_times_unit_value(): void
    {
        $package = $this->shipment()->packages()->create([
            'quantity' => 3, 'package_type' => 'carton', 'weight_kg' => 10, 'unit_value' => 25,
        ]);

        $this->assertSame('75.00', $package->amount);
    }

    public function test_amount_cannot_be_set_directly_via_mass_assignment(): void
    {
        $package = $this->shipment()->packages()->create([
            'quantity' => 2, 'package_type' => 'carton', 'weight_kg' => 10, 'unit_value' => 25, 'amount' => 999,
        ]);

        $this->assertSame('50.00', $package->amount);
    }

    private function shipment(): Shipment
    {
        $user = User::create([
            'name' => 'Seed User', 'email' => 'seed@example.com', 'password' => 'password', 'role' => 'admin',
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
            'created_by' => $user->id,
        ]);
    }
}
