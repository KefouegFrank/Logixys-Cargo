<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
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

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);
    }
}
