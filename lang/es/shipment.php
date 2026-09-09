<?php

return [
    'status' => [
        'PENDING' => 'Pendiente',
        'PICKED_UP' => 'Recogido',
        'IN_TRANSIT' => 'En tránsito',
        'AT_CUSTOMS' => 'En aduana',
        'OUT_FOR_DELIVERY' => 'En reparto',
        'DELIVERED' => 'Entregado',
        'ON_HOLD' => 'En espera',
        'RETURNED' => 'Devuelto',
        'CANCELLED' => 'Cancelado',
    ],
    'service_type' => [
        'road' => 'Transporte por carretera',
        'air' => 'Flete aéreo',
        'sea' => 'Flete marítimo',
        'warehousing' => 'Almacenaje y tránsito',
        'customs' => 'Despacho de aduana',
    ],

    'location_type' => [
        'city' => 'Ciudad',
        'port' => 'Puerto',
        'airport' => 'Aeropuerto',
        'warehouse' => 'Almacén',
        'terminal' => 'Terminal',
    ],

    'package_type' => [
        'carton' => 'Caja',
        'caisse' => 'Cajón',
        'palette' => 'Palet',
        'conteneur' => 'Contenedor',
        'enveloppe' => 'Sobre',
        'fut' => 'Bidón',
    ],

    'payment_status' => [
        'unpaid' => 'No pagado',
        'paid' => 'Pagado',
        'partial' => 'Pago parcial',
        'refunded' => 'Reembolsado',
    ],
];
