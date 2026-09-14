<?php

/*
 * Home "about" block. Icons are Heroicons names resolved in the component;
 * copy lives in lang/{locale}/about.php.
 */
return [
    'features' => [
        ['key' => 'reach', 'icon' => 'globe'],
        ['key' => 'secure', 'icon' => 'shield'],
    ],

    // Checklist on the about page's "what we do" band. Plain keys, no icon —
    // every row uses the same check mark.
    'checklist' => ['tracking', 'handling', 'contact', 'support'],
];
