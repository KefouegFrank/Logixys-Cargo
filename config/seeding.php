<?php

return [

    /*
    |--------------------------------------------------------------------------
    | First administrator
    |--------------------------------------------------------------------------
    |
    | UserSeeder creates one admin from these and refuses to run without them,
    | so seeding can never plant a password that is known off the server.
    |
    */

    'admin_email' => env('SEED_ADMIN_EMAIL'),

    'admin_password' => env('SEED_ADMIN_PASSWORD'),

    'admin_name' => env('SEED_ADMIN_NAME', 'Administrateur'),

];
