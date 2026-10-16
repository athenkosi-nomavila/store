<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · {{ config('app.name') }} Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite('resources/css/app.css')
</head>
{{-- The admin area has its own layout: nothing here is shared with the storefront pages. --}}
<body class="min-h-screen bg-zinc-100 font-sans text-zinc-900 antialiased lg:flex">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand-600 focus:px-4 focus:py-3 focus:text-white">Skip to content</a>

    <aside class="bg-zinc-900 text-zinc-300 lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-60 lg:shrink-0 lg:flex-col">
        <div class="flex items-center justify-between gap-4 px-4 py-3 lg:px-5 lg:py-5">
            <x-admin.brand />

            {{-- Small screens keep the account actions in the top bar. --}}
            <div class="lg:hidden"><x-admin.account-actions /></div>
        </div>

        <nav aria-label="Admin" class="px-2 pb-2 lg:flex-1 lg:px-3">
            <ul class="flex gap-1 overflow-x-auto lg:flex-col">
                @foreach ([
                    ['Dashboard', 'grid', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
                    ['Products', 'box', route('admin.products.index'), request()->routeIs('admin.products.*')],
                    ['Users', 'users', route('admin.users.index'), request()->routeIs('admin.users.*')],
                ] as [$label, $icon, $url, $active])
                    <li>
                        <a href="{{ $url }}" @if ($active) aria-current="page" @endif @class([
                            'flex h-11 items-center gap-3 whitespace-nowrap rounded-lg px-3 text-sm font-medium',
                            'bg-white/10 text-white' => $active,
                            'hover:bg-white/5 hover:text-white' => ! $active,
                        ])><x-icon :name="$icon" />{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="hidden border-t border-white/10 p-3 lg:block">
            <p class="truncate px-3 pb-2 text-xs text-zinc-500">Logged in as <span class="text-zinc-300">{{ auth()->user()->name }}</span></p>
            <x-admin.account-actions stacked />
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <main id="main" tabindex="-1" class="mx-auto max-w-6xl px-4 py-6 focus:outline-none sm:px-6 lg:px-8 lg:py-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold">@yield('title')</h1>
                @yield('actions')
            </div>

            @if (session('status'))
                <p class="mb-6 rounded-lg bg-brand-50 px-4 py-3 text-brand-700" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-red-700" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
