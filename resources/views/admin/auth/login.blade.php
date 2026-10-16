@extends('layouts.admin-guest')

@section('title', 'Log in')

@section('content')
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600"><x-icon name="lock" size="22" /></span>
    <h1 class="mt-5 text-2xl font-bold">Admin login</h1>
    <p class="mt-1 text-sm text-zinc-500">Log in to manage the store. For store administrators only.</p>

    <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-4">
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

        <button type="submit"
                class="h-11 w-full rounded-full bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
            Log in
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500">
        New here? <a href="{{ route('admin.register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Create an account</a>
    </p>
@endsection
