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

    /*
     * Full ramps, byte-identical to the @theme block in resources/css/theme.css.
     * PHP needs them too because Filament takes palettes as arrays, not a base hex.
     */
    'ramps' => [
        'navy' => [
            50 => '#f0f6fd',
            100 => '#e0e9f4',
            200 => '#c7d6e8',
            300 => '#a8bdd6',
            400 => '#849ebd',
            500 => '#6380a2',
            600 => '#466285',
            700 => '#2e496a',
            800 => '#1d3655',
            900 => '#102946',
            950 => '#03162d',
        ],
        'gold' => [
            50 => '#fdfaed',
            100 => '#fbf2ce',
            200 => '#fbe99f',
            300 => '#fbdf6d',
            400 => '#f9d52a',
            500 => '#e0bb00',
            600 => '#bf9b00',
            700 => '#9e7d00',
            800 => '#7d6000',
            900 => '#634a00',
            950 => '#3c2c00',
        ],
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
        'email' => 'info@logixyscargo.fr',
        'phone' => '+33 0 00 00 00 00',
        'phone_href' => '+33000000000',
        'hours_weekday' => 'Lun – Ven : 08h00 – 18h00',
        'hours_weekend' => 'Sam – Dim : fermé',
        // Where the contact page drops its pin. Paris until the client confirms the
        // office; both are editable from the admin Settings page.
        'map_lat' => '48.8566',
        'map_lng' => '2.3522',
    ],

    'fonts' => [
        'heading' => 'Rubik',
        'body' => 'Krub',
    ],
];
