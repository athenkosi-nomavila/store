@extends('layouts.admin')

@section('title', 'Users')

@php
    $input = 'h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30 aria-[invalid=true]:border-red-500';
@endphp

@section('content')
    <div class="grid items-start gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <p class="mb-4 text-sm text-zinc-500">Everyone here can log in to the admin. There is no public sign-up, so new admins are added with the form.</p>

            <div class="relative overflow-x-auto rounded-xl border border-zinc-200 bg-white">
                <table class="w-full min-w-[34rem] text-left text-sm">
                    <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Name</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Email</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Added</th>
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-semibold">
                                    {{ $user->name }}
                                    @if ($user->is(auth()->user()))
                                        <span class="ml-1 text-xs font-normal text-zinc-500">(you)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-zinc-500">{{ $user->created_at->format('j M Y') }}</td>
                                <td class="px-4 py-3">
                                    @unless ($user->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="flex justify-end">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-10 items-center gap-1.5 rounded-full border border-zinc-300 px-4 text-sm font-semibold text-red-600 hover:border-red-300 hover:bg-red-50">
                                                <x-icon name="trash" size="16" /> Remove<span class="sr-only"> {{ $user->name }}</span>
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 rounded-xl border border-zinc-200 bg-white p-6">
            @csrf
            <h2 class="font-semibold">Add an admin</h2>

            @foreach ([
                ['name', 'Full name', 'text', 'off'],
                ['email', 'Email', 'email', 'off'],
                ['password', 'Password', 'password', 'new-password'],
                ['password_confirmation', 'Confirm password', 'password', 'new-password'],
            ] as [$field, $label, $type, $autocomplete])
                <div>
                    <label for="{{ $field }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" type="{{ $type }}" name="{{ $field }}" required autocomplete="{{ $autocomplete }}"
                           @if ($type !== 'password') value="{{ old($field) }}" @endif
                           @if ($field === 'password') minlength="8" @endif
                           @error($field) aria-invalid="true" @enderror
                           class="{{ $input }}">
                </div>
            @endforeach
            <p class="text-xs text-zinc-500">At least 8 characters. Share it with them privately.</p>

            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-full bg-brand-600 font-semibold text-white hover:bg-brand-700">
                <x-icon name="plus" size="18" /> Add admin
            </button>
        </form>
    </div>
@endsection
