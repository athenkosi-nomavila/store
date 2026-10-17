<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;

/**
 * Creates the first admin from ADMIN_NAME, ADMIN_EMAIL and ADMIN_PASSWORD in
 * .env, so no login details live in the code. Other admins are then added
 * from the Users page.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $details = config('admin');

        $validator = Validator::make($details, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->command?->warn('No admin created: set ADMIN_EMAIL and ADMIN_PASSWORD (8+ characters) in .env, then run php artisan db:seed.');

            return;
        }

        // Only creates the account; running the seeder again never resets a
        // password that was changed later.
        $user = User::firstOrNew(['email' => strtolower($details['email'])]);

        if ($user->exists) {
            $this->command?->info("Admin {$user->email} already exists.");

            return;
        }

        $user->fill(['name' => $details['name'], 'password' => $details['password']]);
        $user->forceFill(['is_admin' => true])->save();

        $this->command?->info("Admin {$user->email} created.");
    }
}
