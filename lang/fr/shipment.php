<?php

return [
    'status' => [
        'PENDING' => 'En attente',
        'PICKED_UP' => 'Pris en charge',
        'IN_TRANSIT' => 'En transit',
        'AT_CUSTOMS' => 'En douane',
        'OUT_FOR_DELIVERY' => 'En cours de livraison',
        'DELIVERED' => 'Livre',
        'ON_HOLD' => 'En attente de traitement',
        'RETURNED' => 'Retourne',
        'CANCELLED' => 'Annule',
    ],

    'service_type' => [
        'road' => 'Transport routier',
        'air' => 'Fret aerien',
        'sea' => 'Fret maritime',
        'warehousing' => 'Entreposage et transit',
        'customs' => 'Dedouanement',
    ],

    'shipment_mode' => [
        'door_to_door' => 'Porte a porte',
        'door_to_port' => 'Porte a port',
        'port_to_port' => 'Port a port',
    ],
];
