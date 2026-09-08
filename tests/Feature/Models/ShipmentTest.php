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

    public function test_totals_supplied_explicitly_win_over_the_calculator(): void
    {
        $shipment = $this->makeShipment([
            'freight_cost' => 100, 'insurance_cost' => 0, 'customs_cost' => 0, 'other_cost' => 0, 'tax_rate' => 20,
            'total_ht' => 999, 'tax_amount' => 999, 'total_ttc' => 999,
        ]);

        $this->assertSame('999.00', $shipment->total_ttc);
    }

    public function test_package_aggregates_are_rebuilt_from_the_rows_on_demand(): void
    {
        $shipment = $this->makeShipment();

        $shipment->packages()->create([
            'quantity' => 2, 'package_type' => 'palette', 'weight_kg' => 340,
            'length_cm' => 120, 'width_cm' => 100, 'height_cm' => 150, 'unit_value' => 800,
        ]);

        $shipment->recalculatePackageAggregates();
        $shipment->refresh();

        $this->assertSame(1, $shipment->package_count);
        // weight_kg is per piece, so the line is 2 x 340.
        $this->assertSame('680.00', $shipment->total_weight_kg);
        $this->assertSame('3.600', $shipment->total_volume_cbm);
        $this->assertSame('1600.00', $shipment->declared_value);
    }

    public function test_package_aggregates_drop_back_to_zero_once_the_rows_are_gone(): void
    {
        $shipment = $this->makeShipment();

        $package = $shipment->packages()->create([
            'quantity' => 1, 'package_type' => 'carton', 'weight_kg' => 60, 'unit_value' => 100,
        ]);

        $package->delete();
        $shipment->recalculatePackageAggregates();
        $shipment->refresh();

        $this->assertSame(0, $shipment->package_count);
        $this->assertSame('0.00', $shipment->total_weight_kg);
        $this->assertSame('0.00', $shipment->declared_value);
    }

    public function test_origin_and_destination_are_geocoded_when_coordinates_are_omitted(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::sequence()
                ->push([['lat' => '48.8566', 'lon' => '2.3522']])
                ->push([['lat' => '45.7640', 'lon' => '4.8357']]),
        ]);

        $shipment = $this->makeShipment([
            'origin_lat' => null, 'origin_lng' => null,
            'destination_lat' => null, 'destination_lng' => null,
        ]);

        $this->assertSame('48.8566000', $shipment->origin_lat);
        $this->assertSame('2.3522000', $shipment->origin_lng);
        $this->assertSame('45.7640000', $shipment->destination_lat);
        $this->assertSame('4.8357000', $shipment->destination_lng);
        $this->assertNotNull($shipment->distance_km);
    }

    public function test_geocoding_failure_leaves_coordinates_and_distance_null(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([])]);

        $shipment = $this->makeShipment([
            'origin_lat' => null, 'origin_lng' => null,
            'destination_lat' => null, 'destination_lng' => null,
        ]);

        $this->assertNull($shipment->origin_lat);
        $this->assertNull($shipment->destination_lat);
        $this->assertNull($shipment->distance_km);
    }

    public function test_moving_the_origin_on_an_edit_refreshes_coordinates_and_distance(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([['lat' => '4.0511', 'lon' => '9.7679']]),
        ]);

        $shipment = $this->makeShipment();
        $before = $shipment->distance_km;

        $shipment->update(['origin_label' => 'Douala, CM', 'origin_lat' => null, 'origin_lng' => null]);

        $this->assertSame('4.0511000', $shipment->origin_lat);
        $this->assertSame('9.7679000', $shipment->origin_lng);
        $this->assertNotSame($before, $shipment->distance_km);
    }

    public function test_a_save_that_leaves_the_route_alone_never_calls_the_geocoder(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response([['lat' => '1', 'lon' => '1']])]);

        $shipment = $this->makeShipment();

        $shipment->update(['goods_description' => 'Pieces detachees']);

        Http::assertNothingSent();
    }

    public function test_overdue_scope_matches_a_past_delivery_date_still_in_flight(): void
    {
        $overdue = $this->makeShipment([
            'status' => ShipmentStatus::InTransit,
            'expected_delivery_date' => now()->subDays(2),
        ]);
        $this->makeShipment([
            'status' => ShipmentStatus::InTransit,
            'expected_delivery_date' => now()->addDays(2),
        ]);
        $this->makeShipment([
            'status' => ShipmentStatus::Delivered,
            'expected_delivery_date' => now()->subDays(2),
        ]);
        $this->makeShipment([
            'status' => ShipmentStatus::Cancelled,
            'expected_delivery_date' => now()->subDays(2),
        ]);
        $this->makeShipment(['status' => ShipmentStatus::InTransit, 'expected_delivery_date' => null]);

        $this->assertSame([$overdue->id], Shipment::overdue()->pluck('id')->all());
    }

    private function makeShipment(array $overrides = []): Shipment
    {
        $user = User::firstOrCreate(
            ['email' => 'seed@example.com'],
            ['name' => 'Seed User', 'password' => 'password', 'role' => 'admin'],
        );

        return Shipment::create(array_merge([
            'tracking_number' => 'LGXY'.fake()->bothify('#########').'-CARGO',
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ], $overrides));
    }
}
