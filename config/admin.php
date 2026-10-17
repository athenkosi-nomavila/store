<?php

return [

    /*
    | The admin account, created by `php artisan db:seed` (or
    | `migrate --seed`). Set these in .env; .env.example has a simple
    | starter login. Seeding again does not change an existing password.
    */

    'name' => env('ADMIN_NAME', 'Store Admin'),
    'email' => env('ADMIN_EMAIL'),
    'password' => env('ADMIN_PASSWORD'),

];
