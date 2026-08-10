<?php

namespace Tests\Feature\Filament;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShipmentsTableTest extends TestCase
{
    use RefreshDatabase;

    private ?User $creator = null;

    public function test_search_finds_shipment_by_tracking_number(): void
    {
        $this->actingAs($this->admin());
        $target = $this->shipment(['tracking_number' => 'LGXYFINDMEXXX']);
        $other = $this->shipment(['tracking_number' => 'LGXYOTHERYYYY']);

        Livewire::test(ListShipments::class)
            ->searchTable('LGXYFINDMEXXX')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_search_finds_shipment_by_receiver_company(): void
    {
        $this->actingAs($this->admin());
        $target = $this->shipment(['receiver_company' => 'Acme Freight SARL']);
        $other = $this->shipment(['receiver_company' => 'Other Co']);

        Livewire::test(ListShipments::class)
            ->searchTable('Acme Freight')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_filter_by_status(): void
    {
        $this->actingAs($this->admin());
        $delivered = $this->shipment(['status' => ShipmentStatus::Delivered]);
        $pending = $this->shipment(['status' => ShipmentStatus::Pending]);

        Livewire::test(ListShipments::class)
            ->filterTable('status', ShipmentStatus::Delivered)
            ->assertCanSeeTableRecords([$delivered])
            ->assertCanNotSeeTableRecords([$pending]);
    }

    public function test_filter_by_service_type(): void
    {
        $this->actingAs($this->admin());
        $air = $this->shipment(['service_type' => ServiceType::Air]);
        $road = $this->shipment(['service_type' => ServiceType::Road]);

        Livewire::test(ListShipments::class)
            ->filterTable('service_type', ServiceType::Air)
            ->assertCanSeeTableRecords([$air])
            ->assertCanNotSeeTableRecords([$road]);
    }

    public function test_filter_by_created_date_range(): void
    {
        $this->actingAs($this->admin());
        $old = $this->shipment();
        $old->forceFill(['created_at' => now()->subDays(30)])->saveQuietly();
        $recent = $this->shipment();

        Livewire::test(ListShipments::class)
            ->filterTable('created_between', ['created_from' => now()->subDays(2)->toDateString()])
            ->assertCanSeeTableRecords([$recent])
            ->assertCanNotSeeTableRecords([$old]);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);
    }

    private function shipment(array $overrides = []): Shipment
    {
        $this->creator ??= User::create([
            'name' => 'Agent', 'email' => 'agent@example.com', 'password' => 'password', 'role' => UserRole::Agent,
        ]);

        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->unique()->bothify('#########'),
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'destination_label' => 'Lyon',
            'created_by' => $this->creator->id,
            ...$overrides,
        ]);
    }
}
