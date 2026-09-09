<?php

namespace Tests\Feature\Filament;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Models\PaymentMode;
use App\Models\Shipment;
use App\Models\ShipmentMode;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class ManagedOptionListsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
    }

    public function test_the_three_seeded_modes_exist(): void
    {
        $this->assertSame(
            [ShipmentMode::DOOR_TO_DOOR, ShipmentMode::DOOR_TO_PORT, ShipmentMode::PORT_TO_PORT],
            ShipmentMode::orderBy('id')->pluck('name')->all(),
        );
    }

    public function test_a_mode_added_by_the_office_persists_and_can_be_reused(): void
    {
        $mode = ShipmentMode::create(['name' => 'Port à porte']);

        $shipment = $this->shipment(['shipment_mode_id' => $mode->id, 'shipment_mode' => $mode->name]);

        $this->assertSame('Port à porte', $shipment->fresh()->shipmentMode->name);
        $this->assertSame('Port à porte', $shipment->fresh()->shipment_mode);
    }

    public function test_two_modes_cannot_share_a_name(): void
    {
        ShipmentMode::create(['name' => 'Port à porte']);

        $this->expectException(QueryException::class);
        ShipmentMode::create(['name' => 'Port à porte']);
    }

    public function test_choosing_a_mode_copies_its_name_onto_the_shipment(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $mode = ShipmentMode::create(['name' => 'Aéroport à porte']);
        $shipment = $this->shipment();

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm(['shipment_mode_id' => $mode->id])
            ->call('save')
            ->assertHasNoFormErrors();

        // The copy is what the tracking page and the PDFs print.
        $this->assertSame('Aéroport à porte', $shipment->refresh()->shipment_mode);
    }

    public function test_a_shipment_with_no_mode_row_can_still_be_edited(): void
    {
        $this->actingAs($this->admin());

        // How the seeder and any import leave a row: the name, no foreign key.
        $shipment = $this->shipment(['shipment_mode_id' => null, 'shipment_mode' => ShipmentMode::PORT_TO_PORT]);

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm(['carrier_reference' => 'REF-9'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('REF-9', $shipment->refresh()->carrier_reference);
    }

    public function test_the_four_seeded_payment_modes_exist(): void
    {
        $this->assertSame(
            [PaymentMode::VIREMENT, PaymentMode::ESPECES, PaymentMode::CARTE, PaymentMode::CREDIT],
            PaymentMode::orderBy('id')->pluck('name')->all(),
        );
    }

    public function test_choosing_a_payment_mode_copies_its_name_onto_the_shipment(): void
    {
        $this->actingAs($this->admin());

        $mode = PaymentMode::create(['name' => 'Mobile money']);
        $shipment = $this->shipment();

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm(['payment_mode_id' => $mode->id])
            ->call('save')
            ->assertHasNoFormErrors();

        // The copy is what the tracking page and the waybill print.
        $this->assertSame('Mobile money', $shipment->refresh()->payment_mode);
    }

    public function test_two_payment_modes_cannot_share_a_name(): void
    {
        PaymentMode::create(['name' => 'Mobile money']);

        $this->expectException(QueryException::class);
        PaymentMode::create(['name' => 'Mobile money']);
    }

    public function test_a_shipment_can_be_saved_with_no_payment_mode(): void
    {
        $this->actingAs($this->admin());

        $shipment = $this->shipment(['payment_mode' => null, 'payment_mode_id' => null]);

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($shipment->refresh()->payment_mode);
    }

    public function test_every_dropdown_offers_the_same_empty_wording(): void
    {
        $html = $this->actingAs($this->admin())->get('/admin/shipments/create')->getContent();

        foreach (['service_type', 'shipment_mode_id', 'payment_mode_id', 'carrier_id', 'origin_location_id'] as $field) {
            $this->assertStringContainsString($field, $html);
        }

        $this->assertGreaterThanOrEqual(5, substr_count($html, '-- Choisissez --'));
    }

    /** @param array<string, mixed> $overrides */
    private function shipment(array $overrides = []): Shipment
    {
        $admin = $this->admin();

        return Shipment::create([
            'tracking_number' => 'LGXY'.fake()->unique()->numerify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DOOR_TO_DOOR,
            'shipment_mode_id' => ShipmentMode::firstOrCreate(['name' => ShipmentMode::DOOR_TO_DOOR])->id,
            'locale' => 'fr',
            'shipper_name' => 'Shipper', 'receiver_name' => 'Receiver',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $admin->id,
            ...$overrides,
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
