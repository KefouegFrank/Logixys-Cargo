<?php

namespace Tests\Feature\Models;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_totals_are_computed_from_charges_and_tax_rate_on_create(): void
    {
        $shipment = $this->makeShipment([
            'freight_cost' => 420, 'insurance_cost' => 15, 'customs_cost' => 0, 'other_cost' => 0, 'tax_rate' => 20,
        ]);

        $this->assertSame('435.00', $shipment->total_ht);
        $this->assertSame('87.00', $shipment->tax_amount);
        $this->assertSame('522.00', $shipment->total_ttc);
    }

    public function test_totals_are_recomputed_when_charges_change_on_update(): void
    {
        $shipment = $this->makeShipment([
            'freight_cost' => 100, 'insurance_cost' => 0, 'customs_cost' => 0, 'other_cost' => 0, 'tax_rate' => 20,
        ]);

        $shipment->update(['freight_cost' => 200]);

        $this->assertSame('200.00', $shipment->total_ht);
        $this->assertSame('40.00', $shipment->tax_amount);
        $this->assertSame('240.00', $shipment->total_ttc);
    }

    public function test_total_fields_cannot_be_set_directly_via_mass_assignment(): void
    {
        $shipment = $this->makeShipment([
            'freight_cost' => 100, 'insurance_cost' => 0, 'customs_cost' => 0, 'other_cost' => 0, 'tax_rate' => 20,
            'total_ht' => 999, 'tax_amount' => 999, 'total_ttc' => 999,
        ]);

        $this->assertSame('120.00', $shipment->total_ttc);
    }

    private function makeShipment(array $overrides = []): Shipment
    {
        $user = User::create([
            'name' => 'Seed User', 'email' => 'seed@example.com', 'password' => 'password', 'role' => 'admin',
        ]);

        return Shipment::create(array_merge([
            'tracking_number' => 'LGXY'.fake()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'destination_label' => 'Lyon',
            'created_by' => $user->id,
        ], $overrides));
    }
}
