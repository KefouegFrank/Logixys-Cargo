<?php

namespace App\DataTransferObjects;

use App\Enums\ServiceType;
use App\Enums\ShipmentMode;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use Carbon\CarbonImmutable;

final class PublicShipmentView
{
    /** @param array<int, PublicShipmentEvent> $events */
    private function __construct(
        public readonly string $trackingNumber,
        public readonly ShipmentStatus $status,
        public readonly ServiceType $serviceType,
        public readonly ShipmentMode $shipmentMode,
        public readonly string $shipperMasked,
        public readonly string $shipperCity,
        public readonly string $shipperCountry,
        public readonly string $receiverMasked,
        public readonly string $receiverCity,
        public readonly string $receiverCountry,
        public readonly ?float $originLat,
        public readonly ?float $originLng,
        public readonly ?float $destinationLat,
        public readonly ?float $destinationLng,
        public readonly int $packageCount,
        public readonly float $totalWeightKg,
        public readonly ?string $goodsDescription,
        public readonly ?CarbonImmutable $pickupDate,
        public readonly ?CarbonImmutable $expectedDeliveryDate,
        public readonly array $events,
    ) {}

    public static function fromModel(Shipment $shipment): self
    {
        return new self(
            trackingNumber: $shipment->tracking_number,
            status: $shipment->status,
            serviceType: $shipment->service_type,
            shipmentMode: $shipment->shipment_mode,
            shipperMasked: self::mask($shipment->shipper_name),
            shipperCity: $shipment->shipper_city,
            shipperCountry: $shipment->shipper_country,
            receiverMasked: self::mask($shipment->receiver_name),
            receiverCity: $shipment->receiver_city,
            receiverCountry: $shipment->receiver_country,
            originLat: $shipment->origin_lat !== null ? (float) $shipment->origin_lat : null,
            originLng: $shipment->origin_lng !== null ? (float) $shipment->origin_lng : null,
            destinationLat: $shipment->destination_lat !== null ? (float) $shipment->destination_lat : null,
            destinationLng: $shipment->destination_lng !== null ? (float) $shipment->destination_lng : null,
            packageCount: $shipment->package_count,
            totalWeightKg: (float) $shipment->total_weight_kg,
            goodsDescription: $shipment->goods_description,
            pickupDate: $shipment->pickup_date ? CarbonImmutable::parse($shipment->pickup_date) : null,
            expectedDeliveryDate: $shipment->expected_delivery_date ? CarbonImmutable::parse($shipment->expected_delivery_date) : null,
            events: $shipment->events
                ->where('is_public', true)
                ->sortBy('occurred_at')
                ->map(fn ($event) => PublicShipmentEvent::fromModel($event))
                ->values()
                ->all(),
        );
    }

    private static function mask(string $name): string
    {
        $initials = array_map(
            fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)).'.',
            preg_split('/\s+/', trim($name), flags: PREG_SPLIT_NO_EMPTY) ?: [],
        );

        return implode(' ', $initials);
    }
}
