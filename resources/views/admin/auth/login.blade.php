@extends('layouts.admin-guest')

@section('title', 'Log in')

@section('content')
    <h1 class="text-xl font-bold">Log in to the admin</h1>
    <p class="mt-1 text-sm text-zinc-500">For store administrators only.</p>

    <form method="POST" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="h-11 w-full rounded-lg border border-zinc-300 px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30">
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="h-11 w-full rounded-lg border border-zinc-300 px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30">
        </div>

        {{-- Logging in is wired up in the next step. --}}
        <button type="submit" disabled
                class="h-11 w-full rounded-full bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
            Log in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500">
        New here? <a href="{{ route('admin.register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Create an account</a>
    </p>
@endsection
