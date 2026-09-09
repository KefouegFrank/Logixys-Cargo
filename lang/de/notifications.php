<?php

return [
    'shipment' => [
        'subject' => [
            'created' => 'Sendung erfasst · :tracking',
            'status' => ':status · :tracking',
            'updated' => 'Sendung aktualisiert · :tracking',
        ],
        'headline' => [
            'created' => 'Ihre Sendung ist erfasst',
            'updated' => 'Angaben aktualisiert',
        ],
        'line' => [
            'created' => 'Wir haben Ihre Sendung erfasst. Sie hören bei jedem Schritt von uns und können sie jederzeit mit der Nummer unten verfolgen.',
            'updated' => 'Die Angaben zu Ihrer Sendung haben sich geändert. Hier ist der aktuelle Stand.',
        ],
        'greeting' => 'Hallo :name,',
        'intro_shipper' => 'Hier ist der Stand der Sendung :tracking, die Sie uns anvertraut haben.',
        'intro_receiver' => 'Hier ist der Stand der an Sie adressierten Sendung :tracking.',
        'tracking' => 'Sendungsnummer',
        'route' => 'Strecke',
        'status' => 'Status',
        'date' => 'Datum',
        'location' => 'Standort',
        'eta' => 'Voraussichtliche Zustellung',
        'track' => 'Sendung verfolgen',
        'signoff' => 'Vielen Dank für Ihr Vertrauen,',
        'auto' => 'Automatische Nachricht. Antworten Sie auf diese E-Mail, um unser Team zu erreichen.',
        'lines' => [
            'PENDING' => 'Ihre Sendung ist erfasst. Wir organisieren die Abholung.',
            'PICKED_UP' => 'Ihre Sendung wurde von unserem Team abgeholt.',
            'IN_TRANSIT' => 'Ihre Sendung ist unterwegs.',
            'AT_CUSTOMS' => 'Ihre Sendung befindet sich in der Zollabfertigung.',
            'OUT_FOR_DELIVERY' => 'Ihre Sendung ist in Zustellung.',
            'DELIVERED' => 'Ihre Sendung wurde zugestellt.',
            'ON_HOLD' => 'Ihre Sendung ist vorübergehend angehalten. Wir melden uns in Kürze.',
            'RETURNED' => 'Ihre Sendung geht an den Absender zurück.',
            'CANCELLED' => 'Ihre Sendung wurde storniert.',
        ],
    ],
    'shipment_brief' => [
        'subject' => '[:tracking] :headline',
        'kind' => [
            'created' => 'Neue Sendung',
            'status' => 'Status aktualisiert',
            'updated' => 'Sendung bearbeitet',
        ],
        'notified' => 'Benachrichtigt:',
        'nobody' => 'Niemand wurde benachrichtigt: keine brauchbare Adresse bei dieser Sendung.',
        'skipped' => 'Nicht gesendet an:',
        'open' => 'Sendung öffnen',
    ],
];
