<?php

// The public menu. Desktop and mobile both render from this list, so a change
// here moves both. 'route' is a route name; 'label' a translation key.
return [
    'main' => [
        ['route' => 'home', 'label' => 'nav.home'],
        ['route' => 'about', 'label' => 'nav.about'],
        ['route' => 'services', 'label' => 'nav.services'],
        ['route' => 'contact', 'label' => 'nav.contact'],
    ],
];
