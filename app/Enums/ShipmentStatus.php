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
}
