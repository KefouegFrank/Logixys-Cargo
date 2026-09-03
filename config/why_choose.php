<?php

/*
 * The three reason cards and four stat tiles on the home page. Icons are
 * Heroicons names resolved in the respective components.
 *
 * Stat values are business-history claims (years, volumes, headcount) that
 * only the client can verify — unlike the rest of this site's placeholders,
 * these were supplied as specific numbers rather than left blank, so treat
 * them as real content pending confirmation, not invented copy.
 *
 * 'countries' has no confirmed figure yet (todo: replace before launch).
 */
return [
    'reasons' => [
        ['key' => 'safety', 'icon' => 'shield-check'],
        ['key' => 'support', 'icon' => 'clock'],
        ['key' => 'tracking', 'icon' => 'signal'],
    ],

    'stats' => [
        ['key' => 'experience', 'value' => 10, 'suffix' => '+', 'icon' => 'trophy'],
        ['key' => 'packages', 'value' => 50, 'suffix' => '+', 'icon' => 'cube'],
        ['key' => 'clients', 'value' => 80, 'suffix' => '+', 'icon' => 'user-group'],
        ['key' => 'countries', 'value' => 15, 'suffix' => '+', 'icon' => 'globe-alt'],
    ],
];
