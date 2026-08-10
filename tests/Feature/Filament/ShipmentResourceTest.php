<?php

namespace Tests\Feature\Filament;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_load_the_shipment_list_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/shipments')
            ->assertOk();
    }

    public function test_admin_can_load_the_shipment_create_page(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/shipments/create')
            ->assertOk();
    }

    public function test_admin_can_load_the_shipment_edit_page_with_packages_relation_manager(): void
    {
        $admin = $this->admin();

        $shipment = Shipment::create([
            'tracking_number' => 'LGXY'.fake()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'destination_label' => 'Lyon',
            'created_by' => $admin->id,
        ]);

        $shipment->packages()->create([
            'quantity' => 2, 'package_type' => 'carton', 'weight_kg' => 10,
            'length_cm' => 30, 'width_cm' => 20, 'height_cm' => 20, 'unit_value' => 50,
        ]);

        $this->actingAs($admin)
            ->get("/admin/shipments/{$shipment->id}/edit")
            ->assertOk();
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);
    }
}
