<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(8)],
        ]);

        // While the store has no admin, the first person to sign up becomes
        // it. The lock stops two sign-ups at the same moment both getting in.
        $user = Cache::lock('first-admin-sign-up', 10)->block(5, function () use ($data) {
            $user = User::create($data); // the password is hashed by the model cast

            if (! User::where('is_admin', true)->exists()) {
                $user->forceFill(['is_admin' => true])->save();
            }

            return $user;
        });

        if (! $user->isAdmin()) {
            return redirect()->route('admin.login')
                ->with('status', 'Account created. You can log in once an admin approves it.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('status', 'Welcome! As the first account, you are the store admin.');
    }
}
