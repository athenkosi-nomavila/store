<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') · {{ config('app.name') }} Admin</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite('resources/css/app.css')
</head>
{{-- Admin pages for signed-out visitors (the login page). --}}
<body class="min-h-screen bg-cream font-sans text-zinc-900 antialiased lg:grid lg:grid-cols-2">
    {{-- Brand panel: a full column on large screens, a slim bar on phones. --}}
    <aside class="relative overflow-hidden bg-zinc-900 text-zinc-300">
        <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-brand-600/40 blur-3xl" aria-hidden="true"></div>
        <div class="absolute -bottom-40 -right-24 h-96 w-96 rounded-full bg-brand-700/30 blur-3xl" aria-hidden="true"></div>

        <div class="relative flex h-full flex-col px-6 py-4 lg:px-12 lg:py-10">
            <x-admin.brand />

            <div class="hidden flex-1 flex-col justify-center py-12 lg:flex">
                <div class="mb-10 flex items-end gap-4" aria-hidden="true">
                    @foreach ([['headphones', '-rotate-6 h-32 w-32'], ['watch', 'h-40 w-40'], ['lamp', 'rotate-6 h-32 w-32']] as [$image, $classes])
                        <div class="{{ $classes }} shrink-0 overflow-hidden rounded-2xl shadow-2xl ring-4 ring-white/10">
                            <img src="{{ asset("images/products/{$image}.jpg") }}" alt="" class="h-full w-full object-cover">
                        </div>
                    @endforeach
                </div>

                <h2 class="max-w-md text-4xl font-bold leading-tight text-white">Everything your store needs, in one place.</h2>
                <p class="mt-4 max-w-md text-zinc-400">Add products, keep stock up to date and see how the store is doing. Changes show on the shop straight away.</p>

                <ul class="mt-8 space-y-4">
                    @foreach ([
                        ['box', 'Manage products, prices and stock'],
                        ['grid', 'Live numbers on your dashboard'],
                        ['check', 'Only store administrators can log in'],
                    ] as [$icon, $text])
                        <li class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-brand-50"><x-icon :name="$icon" size="18" /></span>
                            <span class="text-zinc-200">{{ $text }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="hidden text-sm text-zinc-500 lg:block">&copy; {{ now()->year }} {{ config('app.name') }}</p>
        </div>
    </aside>

    <main class="flex items-center justify-center px-4 py-10 sm:px-6 lg:py-16">
        <div class="w-full max-w-md">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-10">
                @if (session('status'))
                    <p class="mb-6 rounded-lg bg-brand-50 px-4 py-3 text-sm text-brand-700" role="status">{{ session('status') }}</p>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </main>
</body>
</html>
