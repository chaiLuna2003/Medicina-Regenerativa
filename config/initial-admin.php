<?php

return [
    'enabled' => (bool) env('INITIAL_ADMIN_ENABLED', false),

    'name' => env(
        'INITIAL_ADMIN_NAME',
        'Administrador'
    ),

    'email' => env('INITIAL_ADMIN_EMAIL'),

    'password' => env('INITIAL_ADMIN_PASSWORD'),
];
