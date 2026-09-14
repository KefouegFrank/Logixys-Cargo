<?php

return [
    'sections' => [
        1 => "Responsable de traitement",
        2 => "Données que nous collectons",
        3 => "Pourquoi nous les utilisons",
        4 => "Destinataires de vos données",
        5 => "Durée de conservation",
        6 => "Cookies",
        7 => "Vos droits",
        8 => "Sécurité",
        9 => "Transferts hors Union européenne",
        10 => "Contact et réclamation",
        11 => "Modifications de la présente politique",
    ],
    'intro' => "Cette politique décrit les données que :name: collecte lorsque vous consultez ce site, nous contactez, ou suivez une expédition, pourquoi, pendant combien de temps, et comment les faire valoir. Elle s'applique au site public — pas à l'espace d'administration réservé au personnel autorisé.",
    'table' => [
        'headers' => ["Donnée", "Finalité", "Base légale"],
        'rows' => [
            ["Formulaire de contact", "Répondre à votre demande", "Intérêt légitime à traiter les demandes qui nous sont adressées"],
            ["Données d'expédition", "Organiser, exécuter et facturer le transport", "Exécution du contrat de transport"],
            ["Factures et documents comptables", "Tenue de la comptabilité", "Obligation légale"],
            ["Adresse IP (formulaire de contact)", "Prévenir les abus (spam, envois automatisés)", "Intérêt légitime à sécuriser le service"],
        ],
    ],
    'articles' => [
        1 => "<p>Le responsable du traitement des données décrites ci-dessous est :name:, dont l'identité complète figure dans les :notice_link:. Pour toute question relative à vos données, écrivez à :email_link:.</p>",
        2 => "<p>Nous collectons uniquement les données nécessaires aux fonctions ci-dessous.</p>
              <ul>
                <li><strong>Formulaire de contact.</strong> Nom, e-mail, téléphone (facultatif), objet et message, ainsi que l'adresse IP et le navigateur utilisé au moment de l'envoi — utiles pour identifier un usage abusif du formulaire.</li>
                <li><strong>Expéditions.</strong> Lorsqu'une expédition est enregistrée pour votre compte, nous conservons le nom, l'e-mail, le téléphone et l'adresse (rue, ville, pays) de l'expéditeur et du destinataire, ainsi que le contenu et le trajet de l'envoi.</li>
                <li><strong>Suivi d'expédition.</strong> La page de suivi affiche l'intégralité de l'enregistrement d'une expédition à quiconque saisit son numéro de suivi — ce numéro fait office d'identifiant d'accès. Ne le communiquez qu'aux personnes concernées par l'envoi.</li>
                <li><strong>Données techniques.</strong> Un cookie de session strictement nécessaire au fonctionnement du site (voir §6). Nous n'utilisons ni cookie publicitaire, ni outil d'analyse d'audience.</li>
              </ul>",
        3 => "<p>Nous traitons chaque catégorie de données pour une raison précise, jamais pour une finalité qu'elle ne justifie pas :</p>",
        4 => "<p>Vos données sont accessibles au personnel autorisé de :name:, et transmises à :</p>
              <ul>
                <li><strong>Resend</strong> (prestataire d'envoi d'e-mails) — pour acheminer les e-mails de confirmation et de notification que nous vous adressons.</li>
                <li><strong>OpenStreetMap</strong> — lorsqu'une carte s'affiche (page de contact, suivi d'expédition), votre navigateur charge les images de la carte directement depuis les serveurs d'OpenStreetMap, ce qui leur transmet votre adresse IP. Aucune autre donnée n'est partagée avec ce service.</li>
                <li><strong>Geoapify / LocationIQ</strong> — outils de recherche d'adresse utilisés en interne par notre équipe lors de la création d'une expédition, pour convertir une adresse en position sur la carte.</li>
              </ul>
              <p>Nous ne vendons ni ne louons vos données à des tiers, et ne les utilisons pas à des fins publicitaires.</p>",
        5 => "<ul>
                <li><strong>Messages du formulaire de contact :</strong> jusqu'à 24 mois à compter du dernier échange, puis suppression.</li>
                <li><strong>Données d'expédition et documents de facturation :</strong> conservés pendant la durée légale de conservation des documents comptables, soit 10 ans en France.</li>
                <li><strong>Cookie de session :</strong> effacé à la fermeture du navigateur ou après :hours: heure(s) d'inactivité.</li>
              </ul>",
        6 => "<p>Ce site dépose un unique cookie, strictement nécessaire à son fonctionnement : un cookie de session qui vous garde connecté d'une page à l'autre et protège les formulaires contre les soumissions frauduleuses (jeton CSRF). Il ne sert ni au suivi publicitaire, ni à la mesure d'audience, et ne nécessite donc pas de consentement au titre de l'article 82 de la loi Informatique et Libertés.</p>
              <p>Nous n'utilisons aucun outil d'analyse d'audience (Google Analytics ou équivalent), ni cookie publicitaire ou de réseau social. Si cela devait changer, un bandeau de consentement conforme serait mis en place avant tout dépôt de cookie non essentiel.</p>",
        7 => "<p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez des droits suivants sur vos données :</p>
              <ul>
                <li><strong>Accès :</strong> obtenir une copie des données que nous détenons sur vous.</li>
                <li><strong>Rectification :</strong> faire corriger des données inexactes ou incomplètes.</li>
                <li><strong>Effacement :</strong> demander la suppression de vos données, dans les limites de nos obligations légales de conservation.</li>
                <li><strong>Limitation :</strong> demander la suspension temporaire d'un traitement.</li>
                <li><strong>Portabilité :</strong> recevoir vos données dans un format structuré et réutilisable.</li>
                <li><strong>Opposition :</strong> vous opposer à un traitement fondé sur notre intérêt légitime.</li>
              </ul>
              <p>Pour exercer l'un de ces droits, écrivez à :email_link:. Nous répondons dans un délai d'un mois. Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la :cnil_link: (Commission Nationale de l'Informatique et des Libertés).</p>",
        8 => "<p>Les échanges avec ce site sont chiffrés (HTTPS). L'accès à l'espace d'administration est réservé au personnel autorisé, protégé par mot de passe, et chaque compte peut être désactivé individuellement. Aucun système n'étant infaillible, nous limitons la collecte aux seules données nécessaires décrites ci-dessus.</p>",
        9 => "<p>Nous cherchons à conserver vos données au sein de l'Union européenne. OpenStreetMap, utilisé pour l'affichage des cartes, est opéré par une fondation basée au Royaume-Uni, pays reconnu par la Commission européenne comme offrant un niveau de protection adéquat des données personnelles.</p>",
        10 => "<p>Pour toute question sur cette politique ou sur vos données, écrivez à :email_link:. Compte tenu de la taille de l'entreprise et de la nature des traitements décrits ci-dessus, la désignation d'un délégué à la protection des données (DPO) n'est pas requise par la réglementation.</p>",
        11 => "<p>Cette politique peut être mise à jour, notamment pour refléter une évolution du site ou de la réglementation. La date de dernière mise à jour figure en haut de cette page ; nous vous invitons à la consulter régulièrement.</p>",
    ],
    'link_notice' => "mentions légales",
];
