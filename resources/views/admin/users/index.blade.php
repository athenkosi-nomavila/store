@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <p class="mb-4 text-sm text-zinc-500">People who sign up wait here until an admin approves them. Only approved admins can open the dashboard.</p>

    <div class="relative overflow-x-auto rounded-xl border border-zinc-200 bg-white">
        <table class="w-full min-w-[40rem] text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold">Name</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Email</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                    <th scope="col" class="px-4 py-3 font-semibold">Signed up</th>
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
                        <td class="px-4 py-3">
                            @if ($user->isAdmin())
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-700"><span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>Admin</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-700"><span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>Waiting for approval</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-zinc-500">{{ $user->created_at->format('j M Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @unless ($user->isAdmin())
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex h-10 items-center gap-1.5 rounded-full bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">
                                            <x-icon name="check" size="16" /> Approve<span class="sr-only"> {{ $user->name }}</span>
                                        </button>
                                    </form>
                                @endunless
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex h-10 items-center gap-1.5 rounded-full border border-zinc-300 px-4 text-sm font-semibold text-red-600 hover:border-red-300 hover:bg-red-50">
                                            <x-icon name="trash" size="16" /> Remove<span class="sr-only"> {{ $user->name }}</span>
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
@endsection
