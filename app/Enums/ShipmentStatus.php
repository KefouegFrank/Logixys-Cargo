<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Pending = 'PENDING';
    case PickedUp = 'PICKED_UP';
    case InTransit = 'IN_TRANSIT';
    case AtCustoms = 'AT_CUSTOMS';
    case OutForDelivery = 'OUT_FOR_DELIVERY';
    case Delivered = 'DELIVERED';
    case OnHold = 'ON_HOLD';
    case Returned = 'RETURNED';
    case Cancelled = 'CANCELLED';

    public function label(): string
    {
        return __('shipment.status.'.$this->value);
    }

    // Null means the status breaks out of the four-step bar into the exception banner.
    public function step(): ?int
    {
        return match ($this) {
            self::Pending, self::PickedUp => 1,
            self::InTransit, self::AtCustoms => 2,
            self::OutForDelivery => 3,
            self::Delivered => 4,
            self::OnHold, self::Returned, self::Cancelled => null,
        };
    }

    public function isException(): bool
    {
        return $this->step() === null;
    }

    public function exceptionSeverity(): ?string
    {
        return match ($this) {
            self::OnHold => 'warning',
            self::Returned, self::Cancelled => 'danger',
            default => null,
        };
    }

    /** @return array<int, self> */
    public static function stepMilestones(): array
    {
        return [1 => self::PickedUp, 2 => self::InTransit, 3 => self::OutForDelivery, 4 => self::Delivered];
    }
}
