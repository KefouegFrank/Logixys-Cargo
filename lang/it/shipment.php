<?php

return [
    'status' => [
        'PENDING' => 'In attesa',
        'PICKED_UP' => 'Ritirato',
        'IN_TRANSIT' => 'In transito',
        'AT_CUSTOMS' => 'In dogana',
        'OUT_FOR_DELIVERY' => 'In consegna',
        'DELIVERED' => 'Consegnato',
        'ON_HOLD' => 'Sospeso',
        'RETURNED' => 'Restituito',
        'CANCELLED' => 'Annullato',
    ],
    'service_type' => [
        'road' => 'Trasporto su strada',
        'air' => 'Trasporto aereo',
        'sea' => 'Trasporto marittimo',
        'warehousing' => 'Stoccaggio e transito',
        'customs' => 'Sdoganamento',
    ],

    'location_type' => [
        'city' => 'Città',
        'port' => 'Porto',
        'airport' => 'Aeroporto',
        'warehouse' => 'Magazzino',
        'terminal' => 'Terminal',
    ],

    'package_type' => [
        'carton' => 'Scatola',
        'caisse' => 'Cassa',
        'palette' => 'Pallet',
        'conteneur' => 'Container',
        'enveloppe' => 'Busta',
        'fut' => 'Fusto',
    ],

    'payment_status' => [
        'unpaid' => 'Non pagato',
        'paid' => 'Pagato',
        'partial' => 'Parzialmente pagato',
        'refunded' => 'Rimborsato',
    ],
];
