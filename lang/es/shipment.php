<?php

return [
    'status' => [
        'PENDING' => "Pendiente",
        'PICKED_UP' => "Recogido",
        'IN_TRANSIT' => "En tránsito",
        'AT_CUSTOMS' => "En aduana",
        'OUT_FOR_DELIVERY' => "En reparto",
        'DELIVERED' => "Entregado",
        'ON_HOLD' => "En espera",
        'RETURNED' => "Devuelto",
        'CANCELLED' => "Cancelado",
    ],
    'service_type' => [
        'road' => "Transporte por carretera",
        'air' => "Flete aéreo",
        'sea' => "Flete marítimo",
        'warehousing' => "Almacenaje y tránsito",
        'customs' => "Despacho de aduana",
    ],
    'shipment_mode' => [
        'door_to_door' => "Puerta a puerta",
        'door_to_port' => "Puerta a puerto",
        'port_to_port' => "Puerto a puerto",
    ],
];
