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

    'location_type' => [
        'city' => 'City',
        'port' => 'Port',
        'airport' => 'Airport',
        'warehouse' => 'Warehouse',
        'terminal' => 'Terminal',
    ],

    'package_type' => [
        'carton' => 'Box',
        'caisse' => 'Crate',
        'palette' => 'Pallet',
        'conteneur' => 'Container',
        'enveloppe' => 'Envelope',
        'fut' => 'Drum',
    ],

    'payment_status' => [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'partial' => 'Partially paid',
        'refunded' => 'Refunded',
    ],
];
