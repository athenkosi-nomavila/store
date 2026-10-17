<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::orderBy('name')->orderBy('id')->paginate(20),
        ]);
    }

    /** Admins add other admins here; there is no public sign-up. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(8)],
        ]);

        $user = new User($data); // the password is hashed by the model cast
        $user->forceFill(['is_admin' => true])->save();

        return back()->with('status', "{$user->name} can now log in as an admin.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Never let the store end up with nobody able to log in.
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot remove your own account.']);
        }

        $user->delete();

        return back()->with('status', "{$user->name} was removed.");
    }
}
