<?php

/*
 * Home hero slides. Each one points at a different destination rather than
 * duplicating the services page — tracking is served by the persistent search
 * field in the hero itself, so no slide needs to lead there.
 *
 * 'image' is a basename under public/images/hero; the component builds the
 * srcset from the widths below. 'key' indexes lang/{locale}/hero.php.
 */
return [
    'interval' => 7000,

    'widths' => [640, 960, 1280, 1920],

    'slides' => [
        ['key' => 'air', 'image' => 'bg-01', 'route' => 'services'],
        ['key' => 'network', 'image' => 'bg-02', 'route' => 'about'],
        ['key' => 'quote', 'image' => 'bg-03', 'route' => 'contact'],
    ],
];
