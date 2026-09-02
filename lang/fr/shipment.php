<?php

return [
    'status' => [
        'PENDING' => 'En attente',
        'PICKED_UP' => 'Pris en charge',
        'IN_TRANSIT' => 'En transit',
        'AT_CUSTOMS' => 'En douane',
        'OUT_FOR_DELIVERY' => 'En cours de livraison',
        'DELIVERED' => 'Livré',
        'ON_HOLD' => 'En attente de traitement',
        'RETURNED' => 'Retourné',
        'CANCELLED' => 'Annulé',
    ],

    'service_type' => [
        'road' => 'Transport routier',
        'air' => 'Fret aérien',
        'sea' => 'Fret maritime',
        'warehousing' => 'Entreposage et transit',
        'customs' => 'Dédouanement',
    ],

    'shipment_mode' => [
        'door_to_door' => 'Porte à porte',
        'door_to_port' => 'Porte à port',
        'port_to_port' => 'Port à port',
    ],
];
