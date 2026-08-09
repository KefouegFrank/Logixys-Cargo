<?php

namespace Database\Seeders;

use App\Enums\PackageType;
use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Enums\UserRole;
use App\Models\Shipment;
use App\Models\ShipmentEvent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ShipmentSeeder extends Seeder
{
    private const TRACKING_CHARSET = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    public function run(): void
    {
        $agent = User::where('role', UserRole::Agent)->first();

        foreach ($this->lanes() as $lane) {
            $charges = $lane['charges'];
            $totalHt = array_sum($charges);
            $taxAmount = round($totalHt * 0.20, 2);

            $shipment = Shipment::create([
                'tracking_number' => $this->trackingNumber(),
                'status' => end($lane['statuses']),
                'service_type' => $lane['service_type'],
                'shipment_mode' => $lane['shipment_mode'],
                'locale' => 'fr',
                'shipper_name' => $lane['shipper_name'],
                'shipper_city' => $lane['origin_city'],
                'shipper_country' => $lane['origin_country'],
                'receiver_name' => $lane['receiver_name'],
                'receiver_city' => $lane['destination_city'],
                'receiver_country' => $lane['destination_country'],
                'origin_label' => $lane['origin_label'],
                'origin_lat' => $lane['origin_lat'],
                'origin_lng' => $lane['origin_lng'],
                'destination_label' => $lane['destination_label'],
                'destination_lat' => $lane['destination_lat'],
                'destination_lng' => $lane['destination_lng'],
                'distance_km' => $lane['distance_km'],
                'pickup_date' => Carbon::now()->subDays(7),
                'expected_delivery_date' => Carbon::now()->addDays(3),
                'goods_description' => $lane['goods_description'],
                'package_count' => count($lane['packages']),
                'total_weight_kg' => array_sum(array_column($lane['packages'], 'weight_kg')),
                'currency' => 'EUR',
                'freight_cost' => $charges['freight'],
                'insurance_cost' => $charges['insurance'],
                'customs_cost' => $charges['customs'],
                'other_cost' => $charges['other'],
                'total_ht' => $totalHt,
                'tax_amount' => $taxAmount,
                'total_ttc' => $totalHt + $taxAmount,
                'created_by' => $agent->id,
            ]);

            foreach ($lane['packages'] as $package) {
                $shipment->packages()->create($package);
            }

            $this->seedEventHistory($shipment, $lane, $agent);
        }
    }

    private function seedEventHistory(Shipment $shipment, array $lane, User $agent): void
    {
        $occurredAt = Carbon::now()->subDays(7);

        foreach ($lane['statuses'] as $index => $status) {
            $isLast = $index === array_key_last($lane['statuses']);

            ShipmentEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $status,
                'location_label' => $isLast ? $lane['destination_label'] : $lane['origin_label'],
                'location_lat' => $isLast ? $lane['destination_lat'] : $lane['origin_lat'],
                'location_lng' => $isLast ? $lane['destination_lng'] : $lane['origin_lng'],
                'occurred_at' => $occurredAt,
                'remarks' => $lane['remarks'][$status->value] ?? null,
                'is_public' => true,
                'created_by' => $agent->id,
            ]);

            $occurredAt = $occurredAt->clone()->addDays(2);
        }
    }

    private function trackingNumber(): string
    {
        $suffix = '';
        $max = strlen(self::TRACKING_CHARSET) - 1;

        for ($i = 0; $i < 9; $i++) {
            $suffix .= self::TRACKING_CHARSET[random_int(0, $max)];
        }

        return 'LGXY'.$suffix;
    }

    private function lanes(): array
    {
        return [
            [
                'service_type' => ServiceType::Road,
                'shipment_mode' => ShipmentMode::DoorToDoor,
                'shipper_name' => 'Atelier Dubois', 'origin_city' => 'Paris', 'origin_country' => 'FR',
                'receiver_name' => 'Menuiserie Lyonnaise', 'destination_city' => 'Lyon', 'destination_country' => 'FR',
                'origin_label' => 'Paris, France', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
                'destination_label' => 'Lyon, France', 'destination_lat' => 45.7640, 'destination_lng' => 4.8357,
                'distance_km' => 465,
                'goods_description' => 'Pieces detachees industrielles',
                'packages' => [
                    ['quantity' => 2, 'package_type' => PackageType::Palette, 'weight_kg' => 340, 'amount' => 0],
                ],
                'charges' => ['freight' => 420, 'insurance' => 15, 'customs' => 0, 'other' => 0],
                'statuses' => [ShipmentStatus::Pending, ShipmentStatus::PickedUp, ShipmentStatus::InTransit],
                'remarks' => [],
            ],
            [
                'service_type' => ServiceType::Air,
                'shipment_mode' => ShipmentMode::DoorToDoor,
                'shipper_name' => 'Cosmetiques de Provence', 'origin_city' => 'Paris', 'origin_country' => 'FR',
                'receiver_name' => 'Boutique Manhattan', 'destination_city' => 'New York', 'destination_country' => 'US',
                'origin_label' => 'Paris CDG, France', 'origin_lat' => 49.0097, 'origin_lng' => 2.5479,
                'destination_label' => 'New York JFK, USA', 'destination_lat' => 40.6413, 'destination_lng' => -73.7781,
                'distance_km' => 5837,
                'goods_description' => 'Cosmetiques et parfums',
                'packages' => [
                    ['quantity' => 5, 'package_type' => PackageType::Carton, 'weight_kg' => 60, 'amount' => 0],
                ],
                'charges' => ['freight' => 890, 'insurance' => 40, 'customs' => 60, 'other' => 0],
                'statuses' => [ShipmentStatus::Pending, ShipmentStatus::PickedUp, ShipmentStatus::InTransit, ShipmentStatus::OutForDelivery],
                'remarks' => [],
            ],
            [
                'service_type' => ServiceType::Sea,
                'shipment_mode' => ShipmentMode::PortToPort,
                'shipper_name' => 'Materiaux Normands', 'origin_city' => 'Le Havre', 'origin_country' => 'FR',
                'receiver_name' => 'BTP Douala', 'destination_city' => 'Douala', 'destination_country' => 'CM',
                'origin_label' => 'Le Havre, France', 'origin_lat' => 49.4944, 'origin_lng' => 0.1079,
                'destination_label' => 'Douala, Cameroun', 'destination_lat' => 4.0511, 'destination_lng' => 9.7679,
                'distance_km' => 8900,
                'goods_description' => 'Materiaux de construction',
                'packages' => [
                    ['quantity' => 1, 'package_type' => PackageType::Conteneur, 'weight_kg' => 4200, 'amount' => 0],
                ],
                'charges' => ['freight' => 2100, 'insurance' => 90, 'customs' => 150, 'other' => 0],
                'statuses' => [ShipmentStatus::Pending, ShipmentStatus::PickedUp, ShipmentStatus::InTransit, ShipmentStatus::AtCustoms],
                'remarks' => [ShipmentStatus::AtCustoms->value => 'En attente de dedouanement au port de Douala'],
            ],
            [
                'service_type' => ServiceType::Warehousing,
                'shipment_mode' => ShipmentMode::DoorToDoor,
                'shipper_name' => 'Distribution Ile-de-France', 'origin_city' => 'Paris', 'origin_country' => 'FR',
                'receiver_name' => 'Entrepot Logixys', 'destination_city' => 'Paris', 'destination_country' => 'FR',
                'origin_label' => 'Paris, France', 'origin_lat' => 48.8566, 'origin_lng' => 2.3522,
                'destination_label' => 'Entrepot Logixys, Paris', 'destination_lat' => 48.8300, 'destination_lng' => 2.3700,
                'distance_km' => 12,
                'goods_description' => 'Stock saisonnier textile',
                'packages' => [
                    ['quantity' => 10, 'package_type' => PackageType::Caisse, 'weight_kg' => 25, 'amount' => 0],
                ],
                'charges' => ['freight' => 80, 'insurance' => 10, 'customs' => 0, 'other' => 120],
                'statuses' => [ShipmentStatus::Pending, ShipmentStatus::PickedUp, ShipmentStatus::Delivered],
                'remarks' => [],
            ],
            [
                'service_type' => ServiceType::Customs,
                'shipment_mode' => ShipmentMode::PortToPort,
                'shipper_name' => 'Import Export Med', 'origin_city' => 'Marseille', 'origin_country' => 'FR',
                'receiver_name' => 'Entrepot sous douane', 'destination_city' => 'Marseille', 'destination_country' => 'FR',
                'origin_label' => 'Port de Marseille, France', 'origin_lat' => 43.2965, 'origin_lng' => 5.3698,
                'destination_label' => 'Entrepot sous douane, Marseille', 'destination_lat' => 43.3200, 'destination_lng' => 5.3800,
                'distance_km' => 8,
                'goods_description' => 'Textiles importes',
                'packages' => [
                    ['quantity' => 1, 'package_type' => PackageType::Conteneur, 'weight_kg' => 3100, 'amount' => 0],
                ],
                'charges' => ['freight' => 0, 'insurance' => 0, 'customs' => 310, 'other' => 0],
                'statuses' => [ShipmentStatus::Pending, ShipmentStatus::PickedUp, ShipmentStatus::OnHold],
                'remarks' => [ShipmentStatus::OnHold->value => 'Documentation manquante, en attente du client'],
            ],
        ];
    }
}
