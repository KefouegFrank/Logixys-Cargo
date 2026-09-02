<?php

use App\Enums\ServiceType;

/*
 * The four services shown on the home page, in display order. 'type' ties each
 * card to the ServiceType enum so titles reuse the translations that already
 * exist in lang/{locale}/shipment.php rather than duplicating them.
 *
 * Named service_cards, not services: config/services.php is Laravel's own file
 * for third-party credentials and must not be shadowed.
 */
return [
    ['type' => ServiceType::Air, 'image' => 'air', 'icon' => 'plane'],
    ['type' => ServiceType::Sea, 'image' => 'sea', 'icon' => 'ship'],
    ['type' => ServiceType::Road, 'image' => 'road', 'icon' => 'truck'],
    ['type' => ServiceType::Warehousing, 'image' => 'warehousing', 'icon' => 'warehouse'],
];
