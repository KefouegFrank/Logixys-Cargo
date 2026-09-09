<?php

return [
    'shipment' => [
        'subject' => [
            'created' => 'Envoi enregistré · :tracking',
            'status' => ':status · :tracking',
            'updated' => 'Envoi mis à jour · :tracking',
        ],
        'headline' => [
            'created' => 'Votre envoi est enregistré',
            'updated' => 'Informations mises à jour',
        ],
        'line' => [
            'created' => 'Nous avons enregistré votre envoi. Vous recevrez un message à chaque étape, et vous pouvez le suivre à tout moment avec le numéro ci-dessous.',
            'updated' => 'Les informations de votre envoi ont été modifiées. Voici le détail à jour.',
        ],
        'greeting' => 'Bonjour :name,',
        'intro_shipper' => 'Voici où en est l\'envoi :tracking que vous nous avez confié.',
        'intro_receiver' => 'Voici où en est le colis :tracking qui vous est adressé.',
        'tracking' => 'Numéro de suivi',
        'route' => 'Trajet',
        'status' => 'Statut',
        'date' => 'Date',
        'location' => 'Emplacement',
        'eta' => 'Livraison prévue',
        'track' => 'Suivre mon colis',
        'signoff' => 'Merci de votre confiance,',
        'auto' => 'Message automatique. Répondez à cet e-mail pour joindre notre équipe.',
        'lines' => [
            'PENDING' => 'Votre envoi est enregistré. Nous organisons sa prise en charge.',
            'PICKED_UP' => 'Votre envoi a été pris en charge par nos équipes.',
            'IN_TRANSIT' => 'Votre envoi est en route.',
            'AT_CUSTOMS' => 'Votre envoi est en cours de dédouanement.',
            'OUT_FOR_DELIVERY' => 'Votre envoi part en livraison.',
            'DELIVERED' => 'Votre envoi a été livré.',
            'ON_HOLD' => 'Votre envoi est momentanément en attente. Nous revenons vers vous rapidement.',
            'RETURNED' => 'Votre envoi est en cours de retour à l\'expéditeur.',
            'CANCELLED' => 'Votre envoi a été annulé.',
        ],
    ],
    'shipment_brief' => [
        'subject' => '[:tracking] :headline',
        'kind' => [
            'created' => 'Nouvel envoi',
            'status' => 'Statut mis à jour',
            'updated' => 'Envoi modifié',
        ],
        'notified' => 'Notifié à :',
        'nobody' => 'Personne n\'a été notifié : aucune adresse exploitable sur cet envoi.',
        'skipped' => 'Non envoyé à :',
        'open' => 'Ouvrir l\'expédition',
    ],
];
