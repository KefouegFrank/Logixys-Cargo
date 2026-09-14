<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    |
    | Addresses allowed to speak for the client through X-Forwarded-*. Anything
    | not listed here has its forwarded headers ignored, so a visitor cannot
    | claim someone else's address to dodge a rate limit.
    |
    | The default covers the usual layout, nginx terminating TLS on the same
    | host. Set TRUSTED_PROXIES when the proxy sits elsewhere — a load balancer
    | or a CDN — to its address or CIDR range. Never "*": that trusts the header
    | from whoever sends it, which is the same as having no proxy configured.
    |
    */

    'proxies' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TRUSTED_PROXIES', '127.0.0.1,::1')),
    ))),

];
