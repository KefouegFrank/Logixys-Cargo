<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
        'webhook_secret' => env('RESEND_WEBHOOK_SECRET'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
     * Address autocomplete, tried in the order listed. BAN and Photon need no key and
     * carry the load; the two metered services only run when those find nothing.
     */
    'address_search' => [
        // Keyless and free: both are asked on every lookup and their results merged, so a
        // French address and a worldwide place can appear side by side.
        'primary' => array_filter(explode(',', (string) env('ADDRESS_SEARCH_PRIMARY', 'ban,photon'))),
        // Metered: only reached when the pair above finds nothing at all.
        'fallback' => array_filter(explode(',', (string) env('ADDRESS_SEARCH_FALLBACK', 'geoapify,locationiq'))),
        'geoapify_key' => env('GEOAPIFY_API_KEY'),
        'locationiq_key' => env('LOCATIONIQ_API_KEY'),
    ],

    'geocoder' => [
        'provider' => env('GEOCODER_PROVIDER', 'nominatim'),
        'api_key' => env('GEOCODER_API_KEY'),
        'user_agent' => env('GEOCODER_USER_AGENT'),
    ],

];
