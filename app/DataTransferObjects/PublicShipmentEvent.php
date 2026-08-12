<?php

namespace App\DataTransferObjects;

use App\Enums\ShipmentStatus;
use App\Models\ShipmentEvent;
use Carbon\CarbonImmutable;

final class PublicShipmentEvent
{
    public function __construct(
        public readonly ShipmentStatus $status,
        public readonly ?string $locationLabel,
        public readonly ?float $locationLat,
        public readonly ?float $locationLng,
        public readonly CarbonImmutable $occurredAt,
    ) {}

    public static function fromModel(ShipmentEvent $event): self
    {
        return new self(
            status: $event->status,
            locationLabel: $event->location_label,
            locationLat: $event->location_lat !== null ? (float) $event->location_lat : null,
            locationLng: $event->location_lng !== null ? (float) $event->location_lng : null,
            occurredAt: CarbonImmutable::parse($event->occurred_at),
        );
    }
}
