<?php

namespace Tests\Feature\Filament;

use App\Enums\LocationType;
use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Package;
use App\Models\PaymentMode;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\ShipmentMode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Fills every field the admin form exposes and asserts each one survives the round trip
 * to the database — the gap that let total_ht reach a NOT NULL column as null.
 */
class ShipmentRoundTripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '43.2965', 'lon' => '5.3698']])]);
    }

    public function test_every_form_field_persists_on_create(): void
    {
        $this->actingAs($this->admin());
        [$form, $expected] = $this->fullForm();

        Livewire::test(CreateShipment::class)
            ->fillForm($form)
            ->call('create')
            ->assertHasNoFormErrors();

        $shipment = Shipment::firstOrFail();

        foreach ($expected as $column => $value) {
            $actual = $shipment->{$column};
            $actual = $actual instanceof \BackedEnum ? $actual->value : (string) $actual;

            $this->assertSame($value, $actual, "Column [{$column}] did not persist as submitted.");
        }
    }

    public function test_package_rows_persist_with_their_dimensions(): void
    {
        $this->actingAs($this->admin());
        [$form] = $this->fullForm();

        Livewire::test(CreateShipment::class)
            ->fillForm($form)
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Shipment::firstOrFail()->packages()->sole();

        $this->assertSame(3, $package->quantity);
        $this->assertSame('palette', $package->package_type->value);
        $this->assertSame('Jantes alliage', $package->description);
        $this->assertSame('120.00', $package->length_cm);
        $this->assertSame('80.00', $package->width_cm);
        $this->assertSame('100.00', $package->height_cm);
        $this->assertSame('250.00', $package->weight_kg);
        $this->assertSame('400.00', $package->unit_value);
    }

    public function test_edits_persist_across_every_section(): void
    {
        $this->actingAs($this->admin());
        [$form] = $this->fullForm();

        Livewire::test(CreateShipment::class)->fillForm($form)->call('create')->assertHasNoFormErrors();
        $shipment = Shipment::firstOrFail();

        Livewire::test(EditShipment::class, ['record' => $shipment->id])
            ->fillForm([
                'shipper_name' => 'Edited Shipper',
                'freight_cost' => 999,
                'carrier_reference' => 'REF-EDITED',
                'goods_description' => 'Edited goods',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $shipment->refresh();
        $this->assertSame('Edited Shipper', $shipment->shipper_name);
        $this->assertSame('999.00', $shipment->freight_cost);
        $this->assertSame('REF-EDITED', $shipment->carrier_reference);
        $this->assertSame('Edited goods', $shipment->goods_description);
    }

    public function test_deleting_a_shipment_cascades_to_its_packages_and_events(): void
    {
        $this->actingAs($admin = $this->admin());
        [$form] = $this->fullForm();

        Livewire::test(CreateShipment::class)->fillForm($form)->call('create')->assertHasNoFormErrors();
        $shipment = Shipment::firstOrFail();

        $shipment->events()->create([
            'status' => ShipmentStatus::InTransit, 'location_label' => 'Marseille',
            'occurred_at' => now(), 'is_public' => true, 'created_by' => $admin->id,
        ]);

        $this->assertSame(1, $shipment->packages()->count());
        $this->assertSame(1, $shipment->events()->count());

        $shipment->delete();

        $this->assertSame(0, Package::where('shipment_id', $shipment->id)->count());
        $this->assertSame(0, ShipmentEvent::where('shipment_id', $shipment->id)->count());
    }

    /** @return array{0: array<string, mixed>, 1: array<string, string>} */
    private function fullForm(): array
    {
        $carrier = Carrier::create(['name' => 'Audit Carrier', 'code' => 'AUD']);
        $origin = Location::create(['name' => 'Marseille', 'type' => LocationType::Port, 'city' => 'Marseille', 'country' => 'FR', 'lat' => 43.2965, 'lng' => 5.3698]);
        $destination = Location::create(['name' => 'Douala', 'type' => LocationType::Port, 'city' => 'Douala', 'country' => 'CM', 'lat' => 4.0511, 'lng' => 9.7679]);

        $mode = ShipmentMode::firstOrCreate(['name' => ShipmentMode::PORT_TO_PORT]);
        $paymentMode = PaymentMode::firstOrCreate(['name' => PaymentMode::VIREMENT]);

        $form = [
            'service_type' => ServiceType::Sea->value,
            'shipment_mode_id' => $mode->id,
            'carrier_id' => $carrier->id,
            'carrier_name' => 'Audit Carrier',
            'carrier_reference' => 'REF-12345',
            'locale' => 'en',
            'shipper_name' => 'Audit Shipper',
            'shipper_email' => 'shipper@audit.test',
            'shipper_phone' => '0102030405',
            'shipper_address' => '1 Quai du Port',
            'receiver_name' => 'Audit Receiver',
            'receiver_email' => 'receiver@audit.test',
            'receiver_phone' => '0607080910',
            'receiver_address' => '2 Rue Douala',
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'pickup_date' => '2026-10-01',
            'pickup_time' => '08:30',
            'departure_time' => '11:45',
            'expected_delivery_date' => '2026-10-20',
            'goods_description' => 'Pieces detachees',
            'currency' => 'EUR',
            'freight_cost' => 1200,
            'insurance_cost' => 150,
            'customs_cost' => 75,
            'other_cost' => 25,
            'tax_rate' => 20,
            'tax_label' => 'TVA',
            'tax_exemption_note' => 'Exoneration article 262',
            'payment_mode_id' => $paymentMode->id,
            'payment_status' => 'paid',
            'packages' => [
                [
                    'quantity' => 3, 'package_type' => 'palette', 'description' => 'Jantes alliage',
                    'length_cm' => 120, 'width_cm' => 80, 'height_cm' => 100,
                    'weight_kg' => 250, 'unit_value' => 400,
                ],
            ],
        ];

        $expected = [
            'service_type' => 'sea',
            'shipment_mode_id' => (string) $mode->id,
            'carrier_id' => (string) $carrier->id,
            'carrier_name' => 'Audit Carrier',
            'carrier_reference' => 'REF-12345',
            'locale' => 'en',
            'shipper_name' => 'Audit Shipper',
            'shipper_email' => 'shipper@audit.test',
            'shipper_phone' => '0102030405',
            'shipper_address' => '1 Quai du Port',
            'receiver_name' => 'Audit Receiver',
            'receiver_email' => 'receiver@audit.test',
            'receiver_phone' => '0607080910',
            'receiver_address' => '2 Rue Douala',
            'origin_location_id' => (string) $origin->id,
            'destination_location_id' => (string) $destination->id,
            'pickup_time' => '08:30:00',
            'departure_time' => '11:45:00',
            'goods_description' => 'Pieces detachees',
            'currency' => 'EUR',
            'freight_cost' => '1200.00',
            'insurance_cost' => '150.00',
            'customs_cost' => '75.00',
            'other_cost' => '25.00',
            'tax_rate' => '20.00',
            'tax_label' => 'TVA',
            'tax_exemption_note' => 'Exoneration article 262',
            'payment_mode_id' => (string) $paymentMode->id,
            'payment_status' => 'paid',
        ];

        return [$form, $expected];
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );
    }
}
