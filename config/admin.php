<?php

return [

    /*
    | The first admin account, created by `php artisan db:seed` (or
    | `migrate --seed`). Set these in .env; they are never committed.
    | Seeding again does not change an existing account's password.
    */

    'name' => env('ADMIN_NAME', 'Store Admin'),
    'email' => env('ADMIN_EMAIL'),
    'password' => env('ADMIN_PASSWORD'),

];
