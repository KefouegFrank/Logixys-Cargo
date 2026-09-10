<?php

return [
    'layout' => [
        'tagline' => 'Su socio logístico de confianza',
        'auto' => 'Enviado automáticamente al actualizarse su envío.',
    ],
    'shipment' => [
        // Requested verbatim by the client; kept as one line for every
        // notice kind rather than the created/status/updated variants it replaced.
        'subject' => 'Notificación de envío Logixys Cargo Envío n.º :tracking.',
        'headline' => [
            'created' => 'Su envío está registrado',
            'updated' => 'Información actualizada',
        ],
        'line' => [
            'created' => 'Hemos registrado su envío. Le informaremos en cada etapa y puede seguirlo en todo momento con el número que aparece abajo.',
            'updated' => 'Los datos de su envío han cambiado. Este es el detalle actualizado.',
        ],
        'greeting' => 'Hola :name:',
        'intro_shipper' => 'Este es el estado del envío :tracking que nos ha confiado.',
        'intro_receiver' => 'Este es el estado del envío :tracking dirigido a usted.',
        'details_heading' => 'Detalles del envío',
        'service' => 'Tipo de servicio',
        'carrier' => 'Transportista',
        'goods' => 'Mercancías',
        'packages' => 'Bultos',
        'support' => '¿Alguna duda? Responda a este correo y nuestro equipo le atenderá.',
        'tracking' => 'Número de seguimiento',
        'route' => 'Trayecto',
        'status' => 'Estado',
        'date' => 'Fecha',
        'location' => 'Ubicación',
        'eta' => 'Entrega prevista',
        'track' => 'Seguir mi envío',
        'signoff' => 'Gracias por su confianza,',
        'auto' => 'Mensaje automático. Responda a este correo para contactar con nuestro equipo.',
        'lines' => [
            'PENDING' => 'Su envío está registrado. Estamos organizando la recogida.',
            'PICKED_UP' => 'Nuestro equipo ha recogido su envío.',
            'IN_TRANSIT' => 'Su envío está en camino.',
            'AT_CUSTOMS' => 'Su envío está en proceso de despacho aduanero.',
            'OUT_FOR_DELIVERY' => 'Su envío ha salido a reparto.',
            'DELIVERED' => 'Su envío ha sido entregado.',
            'ON_HOLD' => 'Su envío está retenido momentáneamente. Le informaremos en breve.',
            'RETURNED' => 'Su envío está siendo devuelto al remitente.',
            'CANCELLED' => 'Su envío ha sido cancelado.',
        ],
    ],
    'shipment_brief' => [
        'subject' => '[:tracking] :headline',
        'kind' => [
            'created' => 'Nuevo envío',
            'status' => 'Estado actualizado',
            'updated' => 'Envío modificado',
        ],
        'notified' => 'Notificado a:',
        'nobody' => 'No se ha notificado a nadie: no hay ninguna dirección utilizable en este envío.',
        'skipped' => 'No enviado a:',
        'open' => 'Abrir el envío',
    ],
];
