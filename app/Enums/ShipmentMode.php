<?php

namespace App\Enums;

enum ShipmentMode: string
{
    case DoorToDoor = 'door_to_door';
    case DoorToPort = 'door_to_port';
    case PortToPort = 'port_to_port';

    public function label(): string
    {
        return __('shipment.shipment_mode.'.$this->value);
    }
}
