<?php

/*
 * Legal identity of the operating entity — the block every legal document
 * (mentions légales, privacy policy, CGV, invoices) needs to name a real,
 * accountable party. None of this can be invented or inferred; it ships
 * null and is filled from the admin Settings page (see
 * AppServiceProvider::overlayCompanySettings()), the same overlay pattern
 * config/brand.php uses for the public contact details.
 *
 * 'identifiers' is a repeatable label/value list rather than fixed columns
 * (siret, rcs, ...) so a non-French entity's registration numbers fit
 * without a migration — see the project doc, "Company identity block".
 */
return [
    'legal_name' => null,
    'legal_form' => null,
    'share_capital' => null,
    'address' => null,
    'tax_id' => null,
    'identifiers' => [],
    'director' => null,

    'host' => [
        'name' => null,
        'address' => null,
        'phone' => null,
    ],

    // The accredited consumer-mediation body the client has adhered to.
    // Required by Art. L616-1 of the Code de la consommation if the site
    // sells to individual consumers, not just businesses.
    'mediator' => [
        'name' => null,
        'url' => null,
    ],
];
