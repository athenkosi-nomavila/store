<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            // Pending accounts first, so they are easy to spot.
            'users' => User::orderBy('is_admin')->orderByDesc('created_at')->orderByDesc('id')->paginate(20),
        ]);
    }

    public function approve(User $user): RedirectResponse
    {
        $user->forceFill(['is_admin' => true])->save();

        return back()->with('status', "{$user->name} is now an admin.");
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
