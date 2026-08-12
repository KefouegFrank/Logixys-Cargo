<?php

return [
    'status' => [
        'PENDING' => 'Pending',
        'PICKED_UP' => 'Picked up',
        'IN_TRANSIT' => 'In transit',
        'AT_CUSTOMS' => 'At customs',
        'OUT_FOR_DELIVERY' => 'Out for delivery',
        'DELIVERED' => 'Delivered',
        'ON_HOLD' => 'On hold',
        'RETURNED' => 'Returned',
        'CANCELLED' => 'Cancelled',
    ],

    'service_type' => [
        'road' => 'Road freight',
        'air' => 'Air freight',
        'sea' => 'Sea freight',
        'warehousing' => 'Warehousing and transit',
        'customs' => 'Customs clearance',
    ],

    'shipment_mode' => [
        'door_to_door' => 'Door to door',
        'door_to_port' => 'Door to port',
        'port_to_port' => 'Port to port',
    ],
];
