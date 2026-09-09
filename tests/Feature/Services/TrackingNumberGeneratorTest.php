<?php

namespace Tests\Feature\Services;

use App\Enums\ServiceType;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\ShipmentMode;
use App\Models\User;
use App\Services\TrackingNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_produces_the_expected_format(): void
    {
        $number = (new TrackingNumberGenerator)->generate();

        $this->assertMatchesRegularExpression('/^LGXY\d{9}-CARGO$/', $number);
    }

    public function test_generate_retries_when_the_first_candidate_collides(): void
    {
        $this->seedShipmentWithTrackingNumber('LGXY111111111-CARGO');

        $generator = $this->getMockBuilder(TrackingNumberGenerator::class)
            ->onlyMethods(['randomDigits'])
            ->getMock();

        $generator->expects($this->exactly(2))
            ->method('randomDigits')
            ->willReturnOnConsecutiveCalls('111111111', '222222222');

        $this->assertSame('LGXY222222222-CARGO', $generator->generate());
    }

    private function seedShipmentWithTrackingNumber(string $trackingNumber): void
    {
        $user = User::create([
            'name' => 'Seed User',
            'email' => 'seed@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        Shipment::create([
            'tracking_number' => $trackingNumber,
            'status' => ShipmentStatus::Pending,
            'service_type' => ServiceType::Road,
            'shipment_mode' => ShipmentMode::DOOR_TO_DOOR,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
            'destination_label' => 'Lyon', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
            'created_by' => $user->id,
        ]);
    }
}
