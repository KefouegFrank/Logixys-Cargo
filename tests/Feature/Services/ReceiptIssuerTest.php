<?php

namespace Tests\Feature\Services;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Models\Receipt;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Documents\PdfRenderer;
use App\Services\Documents\ReceiptIssuer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReceiptIssuerTest extends TestCase
{
    use RefreshDatabase;

    public function test_issuing_stores_a_numbered_pdf_and_freezes_the_totals(): void
    {
        Storage::fake('local');
        $shipment = $this->shipment(['freight_cost' => 420, 'insurance_cost' => 15, 'tax_rate' => 20]);

        $receipt = app(ReceiptIssuer::class)->issue($shipment);

        $this->assertSame('FA-'.now()->year.'-0001', $receipt->number);
        $this->assertSame('435.00', $receipt->total_ht);
        $this->assertSame('522.00', $receipt->total_ttc);
        Storage::disk('local')->assertExists($receipt->path);
    }

    public function test_asking_twice_returns_the_document_already_issued(): void
    {
        Storage::fake('local');
        $shipment = $this->shipment();

        $first = app(ReceiptIssuer::class)->issue($shipment);
        $second = app(ReceiptIssuer::class)->issue($shipment->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Receipt::count());
    }

    public function test_numbers_run_in_sequence_without_gaps(): void
    {
        Storage::fake('local');

        $numbers = collect(range(1, 3))
            ->map(fn () => app(ReceiptIssuer::class)->issue($this->shipment())->number)
            ->all();

        $year = now()->year;
        $this->assertSame(["FA-{$year}-0001", "FA-{$year}-0002", "FA-{$year}-0003"], $numbers);
    }

    public function test_the_waybill_renders_a_pdf(): void
    {
        $pdf = app(PdfRenderer::class)->waybill($this->shipment());

        $this->assertStringStartsWith('%PDF-', $pdf);
    }

    /** @param array<string, mixed> $attributes */
    private function shipment(array $attributes = []): Shipment
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => UserRole::Admin, 'is_active' => true],
        );

        $shipment = Shipment::create([
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Atelier Dubois', 'shipper_city' => 'Paris',
            'receiver_name' => 'Menuiserie Lyonnaise', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ] + $attributes);

        $shipment->packages()->create([
            'quantity' => 2, 'package_type' => 'palette', 'weight_kg' => 340,
            'length_cm' => 120, 'width_cm' => 100, 'height_cm' => 150, 'unit_value' => 800,
        ]);

        return $shipment->fresh('packages');
    }
}
