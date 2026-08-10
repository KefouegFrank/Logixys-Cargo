<?php

namespace Tests\Feature\Services;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
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

        $this->assertMatchesRegularExpression('/^LGXY[0-9A-HJ-KM-NP-TV-Z]{9}$/', $number);
    }

    public function test_generate_retries_when_the_first_candidate_collides(): void
    {
        $this->seedShipmentWithTrackingNumber('LGXYAAAAAAAAA');

        $generator = $this->getMockBuilder(TrackingNumberGenerator::class)
            ->onlyMethods(['randomSuffix'])
            ->getMock();

        $generator->expects($this->exactly(2))
            ->method('randomSuffix')
            ->willReturnOnConsecutiveCalls('AAAAAAAAA', 'BBBBBBBBB');

        $this->assertSame('LGXYBBBBBBBBB', $generator->generate());
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
            'shipment_mode' => ShipmentMode::DoorToDoor,
            'shipper_name' => 'Shipper', 'shipper_city' => 'Paris',
            'receiver_name' => 'Receiver', 'receiver_city' => 'Lyon', 'receiver_country' => 'FR',
            'origin_label' => 'Paris', 'destination_label' => 'Lyon',
            'created_by' => $user->id,
        ]);
    }
}
