<?php

return [
    'status' => [
        'PENDING' => "In attesa",
        'PICKED_UP' => "Ritirato",
        'IN_TRANSIT' => "In transito",
        'AT_CUSTOMS' => "In dogana",
        'OUT_FOR_DELIVERY' => "In consegna",
        'DELIVERED' => "Consegnato",
        'ON_HOLD' => "Sospeso",
        'RETURNED' => "Restituito",
        'CANCELLED' => "Annullato",
    ],
    'service_type' => [
        'road' => "Trasporto su strada",
        'air' => "Trasporto aereo",
        'sea' => "Trasporto marittimo",
        'warehousing' => "Stoccaggio e transito",
        'customs' => "Sdoganamento",
    ],
    'shipment_mode' => [
        'door_to_door' => "Porta a porta",
        'door_to_port' => "Porta a porto",
        'port_to_port' => "Porto a porto",
    ],
];
