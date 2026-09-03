<?php

return [
    /*
     * Anchor colours sampled from resources/images/image/logo-main.png. These four
     * values are mirrored in resources/css/theme.css, which derives the full ramps
     * for the public site. Change them together.
     */
    'colors' => [
        'navy' => '#102946',
        'gold' => '#F9D52A',
        'ink_inverse' => '#FFFFFF',
        'line' => '#C7D6E8',
    ],

    'logo' => [
        'mark' => 'images/logo-mark.svg',
        'mark_light' => 'images/logo-mark-light.svg',
    ],

    /*
     * Trimmed from logo-main.png. 'light' recolours the navy to white for use
     * on dark surfaces; the gold is untouched in both.
     */
    'lockup' => [
        'dark' => 'images/logo.png',
        'light' => 'images/logo-light.png',
        'width' => 640,
        'height' => 161,
    ],

    // PLACEHOLDER — awaiting the client's real details.
    'contact' => [
        'address' => 'Adresse à confirmer',
        'email' => 'contact@logixyscargo.fr',
        'phone' => '+33 0 00 00 00 00',
        'phone_href' => '+33000000000',
        'hours_weekday' => 'Lun – Ven : 08h00 – 18h00',
        'hours_weekend' => 'Sam – Dim : fermé',
    ],

    'fonts' => [
        'heading' => 'Rubik',
        'body' => 'Krub',
    ],
];
