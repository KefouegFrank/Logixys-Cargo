<?php

return [
    /*
     * (L x W x H in cm) / divisor = volumetric weight in kg. One value for every
     * service type, matching the WP-Cargo screens this admin replaces.
     */
    'volumetric_divisor' => (int) env('SHIPPING_VOLUMETRIC_DIVISOR', 5000),
];
