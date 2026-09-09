<?php

return [
    'shipment' => [
        'subject' => [
            'created' => 'Spedizione registrata · :tracking',
            'status' => ':status · :tracking',
            'updated' => 'Spedizione aggiornata · :tracking',
        ],
        'headline' => [
            'created' => 'La tua spedizione è registrata',
            'updated' => 'Informazioni aggiornate',
        ],
        'line' => [
            'created' => 'Abbiamo registrato la tua spedizione. Ti aggiorneremo a ogni tappa e puoi seguirla in qualsiasi momento con il numero qui sotto.',
            'updated' => 'I dati della tua spedizione sono cambiati. Ecco il dettaglio aggiornato.',
        ],
        'greeting' => 'Ciao :name,',
        'intro_shipper' => 'Ecco a che punto è la spedizione :tracking che ci hai affidato.',
        'intro_receiver' => 'Ecco a che punto è la spedizione :tracking a te indirizzata.',
        'tracking' => 'Numero di tracciamento',
        'route' => 'Tragitto',
        'status' => 'Stato',
        'date' => 'Data',
        'location' => 'Posizione',
        'eta' => 'Consegna prevista',
        'track' => 'Segui la mia spedizione',
        'signoff' => 'Grazie per la fiducia,',
        'auto' => 'Messaggio automatico. Rispondi a questa e-mail per contattare il nostro team.',
        'lines' => [
            'PENDING' => 'La tua spedizione è registrata. Stiamo organizzando il ritiro.',
            'PICKED_UP' => 'La tua spedizione è stata ritirata dal nostro team.',
            'IN_TRANSIT' => 'La tua spedizione è in viaggio.',
            'AT_CUSTOMS' => 'La tua spedizione è in fase di sdoganamento.',
            'OUT_FOR_DELIVERY' => 'La tua spedizione è in consegna.',
            'DELIVERED' => 'La tua spedizione è stata consegnata.',
            'ON_HOLD' => 'La tua spedizione è momentaneamente sospesa. Ti aggiorneremo a breve.',
            'RETURNED' => 'La tua spedizione sta tornando al mittente.',
            'CANCELLED' => 'La tua spedizione è stata annullata.',
        ],
    ],
    'shipment_brief' => [
        'subject' => '[:tracking] :headline',
        'kind' => [
            'created' => 'Nuova spedizione',
            'status' => 'Stato aggiornato',
            'updated' => 'Spedizione modificata',
        ],
        'notified' => 'Notificato a:',
        'nobody' => 'Nessuno è stato avvisato: nessun indirizzo utilizzabile su questa spedizione.',
        'skipped' => 'Non inviato a:',
        'open' => 'Apri la spedizione',
    ],
];
