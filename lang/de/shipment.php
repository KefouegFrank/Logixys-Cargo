<?php

return [
    'status' => [
        'PENDING' => "Ausstehend",
        'PICKED_UP' => "Abgeholt",
        'IN_TRANSIT' => "Unterwegs",
        'AT_CUSTOMS' => "Beim Zoll",
        'OUT_FOR_DELIVERY' => "In Zustellung",
        'DELIVERED' => "Zugestellt",
        'ON_HOLD' => "Angehalten",
        'RETURNED' => "Zurückgesandt",
        'CANCELLED' => "Storniert",
    ],
    'service_type' => [
        'road' => "Straßentransport",
        'air' => "Luftfracht",
        'sea' => "Seefracht",
        'warehousing' => "Lagerung und Transit",
        'customs' => "Zollabfertigung",
    ],
    'shipment_mode' => [
        'door_to_door' => "Haus zu Haus",
        'door_to_port' => "Haus zu Hafen",
        'port_to_port' => "Hafen zu Hafen",
    ],
];
