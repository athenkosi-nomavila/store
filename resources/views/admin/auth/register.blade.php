@extends('layouts.admin-guest')

@section('title', 'Create an account')

@section('content')
    <h1 class="text-xl font-bold">Create an account</h1>
    <p class="mt-1 text-sm text-zinc-500">An existing admin needs to approve your account before you can use the dashboard.</p>

    <form method="POST" class="mt-6 space-y-4">
        @csrf

        @foreach ([
            ['name', 'Full name', 'text', 'name'],
            ['email', 'Email', 'email', 'username'],
            ['password', 'Password', 'password', 'new-password'],
            ['password_confirmation', 'Confirm password', 'password', 'new-password'],
        ] as [$field, $label, $type, $autocomplete])
            <div>
                <label for="{{ $field }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
                <input id="{{ $field }}" type="{{ $type }}" name="{{ $field }}" required autocomplete="{{ $autocomplete }}"
                       @if ($type !== 'password') value="{{ old($field) }}" @endif
                       @if ($field === 'password') minlength="8" aria-describedby="password-help" @endif
                       class="h-11 w-full rounded-lg border border-zinc-300 px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30">
                @if ($field === 'password')
                    <p id="password-help" class="mt-1 text-xs text-zinc-500">At least 8 characters.</p>
                @endif
            </div>
        @endforeach

        {{-- Creating accounts is wired up in the login step. --}}
        <button type="submit" disabled
                class="h-11 w-full rounded-full bg-brand-600 font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-60">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-zinc-500">
        Already have an account? <a href="{{ route('admin.login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Log in</a>
    </p>
@endsection
