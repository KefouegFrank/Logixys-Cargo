<?php

return [
    'status' => [
        'PENDING' => 'Ausstehend',
        'PICKED_UP' => 'Abgeholt',
        'IN_TRANSIT' => 'Unterwegs',
        'AT_CUSTOMS' => 'Beim Zoll',
        'OUT_FOR_DELIVERY' => 'In Zustellung',
        'DELIVERED' => 'Zugestellt',
        'ON_HOLD' => 'Angehalten',
        'RETURNED' => 'Zurückgesandt',
        'CANCELLED' => 'Storniert',
    ],
    'service_type' => [
        'road' => 'Straßentransport',
        'air' => 'Luftfracht',
        'sea' => 'Seefracht',
        'warehousing' => 'Lagerung und Transit',
        'customs' => 'Zollabfertigung',
    ],

    'location_type' => [
        'city' => 'Stadt',
        'port' => 'Hafen',
        'airport' => 'Flughafen',
        'warehouse' => 'Lager',
        'terminal' => 'Terminal',
    ],

    'package_type' => [
        'carton' => 'Karton',
        'caisse' => 'Kiste',
        'palette' => 'Palette',
        'conteneur' => 'Container',
        'enveloppe' => 'Umschlag',
        'fut' => 'Fass',
    ],

    'payment_status' => [
        'unpaid' => 'Nicht bezahlt',
        'paid' => 'Bezahlt',
        'partial' => 'Teilweise bezahlt',
        'refunded' => 'Erstattet',
    ],
];
