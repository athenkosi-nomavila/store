<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Demo endpoint: validates the address and confirms. Nothing is stored
     * until a mailing provider is chosen.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        return back()->with('status', 'Thanks for subscribing. Look out for our next update.');
    }
}
