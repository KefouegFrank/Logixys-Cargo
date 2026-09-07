<?php

namespace Tests\Feature\Filament;

use App\Enums\LocationType;
use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class CreateShipmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Coordinates come from the picked locations, so nothing should reach the geocoder.
        Http::preventStrayRequests();
    }

    public function test_the_form_creates_a_shipment_with_its_packages_in_one_pass(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $form = $this->validForm();

        Livewire::test(CreateShipment::class)
            ->fillForm($form)
            ->call('create')
            ->assertHasNoFormErrors();

        $shipment = Shipment::firstOrFail();

        $this->assertMatchesRegularExpression('/^LGXY\d{9}-CARGO$/', $shipment->tracking_number);
        $this->assertSame(ShipmentStatus::Pending, $shipment->status);
        $this->assertSame($form['carrier_id'], $shipment->carrier_id);
        $this->assertSame($form['origin_location_id'], $shipment->origin_location_id);
        $this->assertSame('08:30:00', $shipment->pickup_time);
        $this->assertSame('11:00:00', $shipment->departure_time);
        $this->assertSame($admin->id, $shipment->created_by);

        $this->assertCount(1, $shipment->packages);
        $this->assertEquals(1, $shipment->package_count);
        $this->assertEquals(30, $shipment->total_weight_kg);
        $this->assertEquals(240, $shipment->declared_value);
    }

    public function test_the_number_shown_on_the_form_is_the_one_that_gets_saved(): void
    {
        $this->actingAs($this->admin());

        $page = Livewire::test(CreateShipment::class);
        $shown = $page->get('data.tracking_number');

        $this->assertMatchesRegularExpression('/^LGXY\d{9}-CARGO$/', $shown);

        $page->fillForm($this->validForm())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame($shown, Shipment::firstOrFail()->tracking_number);
    }

    public function test_every_site_locale_can_be_recorded_as_the_customer_language(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateShipment::class)
            ->fillForm([...$this->validForm(), 'locale' => 'de'])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('de', Shipment::firstOrFail()->locale);
        $this->assertSame(['fr', 'en', 'es', 'it', 'de'], array_keys(config('locales')));
    }

    public function test_the_sidebar_posts_a_backdated_first_tracking_event(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
        $this->actingAs($this->admin());

        Livewire::test(CreateShipment::class)
            ->fillForm([
                ...$this->validForm(),
                'event_status' => ShipmentStatus::PickedUp->value,
                'event_location' => 'Paris',
                'event_date' => '2026-09-05',
                'event_time' => '08:30',
                'event_remarks' => 'Enlèvement effectué par le chauffeur.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $shipment = Shipment::firstOrFail();
        $event = $shipment->events()->sole();

        $this->assertSame(ShipmentStatus::PickedUp, $shipment->status);
        $this->assertSame(ShipmentStatus::PickedUp, $event->status);
        $this->assertSame('Paris', $event->location_label);
        // The timestamp typed in, not the moment the form was submitted.
        $this->assertSame('2026-09-05 08:30', $event->occurred_at->format('Y-m-d H:i'));
        $this->assertSame('Enlèvement effectué par le chauffeur.', $event->remarks);
        $this->assertTrue($event->is_public);
    }

    public function test_a_pin_dragged_after_geocoding_is_the_position_that_is_saved(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '48.8566', 'lon' => '2.3522']])]);
        $this->actingAs($this->admin());

        Livewire::test(CreateShipment::class)
            ->fillForm([
                ...$this->validForm(),
                'event_status' => ShipmentStatus::InTransit->value,
                'event_location' => 'Entrepôt sans adresse',
            ])
            // Geocoding has run; this is the agent dragging the marker afterwards.
            ->set('data.event_position', ['lat' => 45.1, 'lng' => 5.7, 'isManual' => true])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Shipment::firstOrFail()->events()->sole();

        $this->assertSame('45.1000000', $event->location_lat);
        $this->assertSame('5.7000000', $event->location_lng);
        $this->assertTrue($event->is_manual_position);
    }

    public function test_saving_without_a_status_leaves_the_history_untouched(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateShipment::class)
            ->fillForm($this->validForm())
            ->call('create')
            ->assertHasNoFormErrors();

        $shipment = Shipment::firstOrFail();

        $this->assertSame(ShipmentStatus::Pending, $shipment->status);
        $this->assertCount(0, $shipment->events);
    }

    public function test_the_derived_fields_track_the_package_rows(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateShipment::class)
            ->fillForm($this->validForm())
            // 100 x 50 x 50 / 5000 = 50 kg volumetric per unit, x2 units.
            ->assertFormSet([
                'total_weight_kg' => '30.00',
                'package_count' => 1,
                'total_quantity' => 2,
                'volumetric_weight_kg' => '100.00',
                'chargeable_weight_kg' => '100.00',
                'declared_value' => '240.00',
            ]);
    }

    public function test_volumetric_weight_beats_actual_weight_on_a_light_bulky_load(): void
    {
        $shipment = $this->shipmentWithPackage(['length_cm' => 100, 'width_cm' => 50, 'height_cm' => 50, 'weight_kg' => 30, 'quantity' => 1]);

        // 100 x 50 x 50 / 5000 = 50 kg volumetric against 30 kg actual.
        $this->assertSame('50.00', $shipment->volumetric_weight_kg);
        $this->assertSame('50.00', $shipment->chargeable_weight_kg);
    }

    public function test_chargeable_weight_falls_back_to_actual_on_a_dense_load(): void
    {
        $shipment = $this->shipmentWithPackage(['length_cm' => 40, 'width_cm' => 40, 'height_cm' => 40, 'weight_kg' => 400, 'quantity' => 1]);

        $this->assertSame('400.00', $shipment->chargeable_weight_kg);
    }

    /** @return array<string, mixed> */
    private function validForm(): array
    {
        $carrier = Carrier::firstOrCreate(['code' => 'LGX-R'], ['name' => 'Logixys Route']);
        $origin = Location::firstOrCreate(['name' => 'Paris'], ['type' => LocationType::City, 'city' => 'Paris', 'country' => 'FR', 'lat' => 48.8566, 'lng' => 2.3522]);
        $destination = Location::firstOrCreate(['name' => 'Lyon'], ['type' => LocationType::City, 'city' => 'Lyon', 'country' => 'FR', 'lat' => 45.7640, 'lng' => 4.8357]);

        return [
            'carrier_id' => $carrier->id,
            'service_type' => ServiceType::Road->value,
            'shipment_mode' => ShipmentMode::DoorToDoor->value,
            'locale' => 'fr',
            'shipper_name' => 'Atelier Dubois',
            'shipper_city' => 'Paris',
            'shipper_country' => 'FR',
            'receiver_name' => 'Menuiserie Lyonnaise',
            'receiver_city' => 'Lyon',
            'receiver_country' => 'FR',
            'origin_location_id' => $origin->id,
            'origin_label' => 'Paris, FR',
            'origin_lat' => 48.8566,
            'origin_lng' => 2.3522,
            'destination_location_id' => $destination->id,
            'destination_label' => 'Lyon, FR',
            'destination_lat' => 45.7640,
            'destination_lng' => 4.8357,
            'pickup_date' => '2026-09-10',
            'pickup_time' => '08:30',
            'departure_time' => '11:00',
            'expected_delivery_date' => '2026-09-12',
            'currency' => 'EUR',
            'tax_label' => 'TVA',
            'tax_rate' => 20,
            'payment_status' => 'unpaid',
            'freight_cost' => 400,
            'packages' => [
                ['quantity' => 2, 'package_type' => 'carton', 'description' => 'Chaises',
                    'length_cm' => 100, 'width_cm' => 50, 'height_cm' => 50, 'weight_kg' => 30, 'unit_value' => 120],
            ],
        ];
    }

    private function shipmentWithPackage(array $package): Shipment
    {
        $admin = $this->admin();

        $shipment = Shipment::create([
            'tracking_number' => 'LGXY'.fake()->unique()->numerify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $admin->id,
        ]);

        $shipment->packages()->create($package + ['package_type' => 'carton']);
        $shipment->recalculatePackageAggregates();

        return $shipment->fresh('packages');
    }

    private function admin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );
    }
}
