<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Everyday essentials, thoughtfully chosen.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen flex-col bg-cream font-sans text-zinc-900 antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-brand-600 focus:px-4 focus:py-3 focus:text-white">Skip to content</a>

    <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3 sm:px-6">
            <x-logo />

            <form action="{{ route('shop.search') }}" method="GET" role="search" class="relative max-w-xl flex-1 basis-64">
                <label for="site-search" class="sr-only">Search products</label>
                <input id="site-search" type="search" name="q" value="{{ is_string(request()->query('q')) ? request()->query('q') : '' }}"
                       placeholder="Search products..." maxlength="100" autocomplete="off"
                       class="h-11 w-full rounded-full border border-zinc-300 bg-cream pl-4 pr-12 focus:border-brand-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-600/30">
                <button type="submit" aria-label="Search"
                        class="absolute right-1 top-1 flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-white hover:bg-brand-700">
                    <x-icon name="search" />
                </button>
            </form>
        </div>

        <nav class="mx-auto max-w-6xl px-4 sm:px-6" aria-label="Main">
            <ul class="-mb-px flex gap-1 overflow-x-auto">
                @php
                    $navLinks = [['Home', route('home'), request()->routeIs('home')],
                        ['All products', route('shop.index'), request()->routeIs('shop.index', 'shop.search') && ! request()->filled('category')]];
                    foreach ($navCategories as $navCategory) {
                        $navLinks[] = [$navCategory->name, route('shop.category', $navCategory), request()->routeIs('shop.category') && request()->route('slug') === $navCategory->slug];
                    }
                @endphp
                @foreach ($navLinks as [$label, $url, $active])
                    <li>
                        <a href="{{ $url }}" @class([
                            'flex h-11 items-center whitespace-nowrap border-b-2 px-3 text-sm font-medium',
                            'border-brand-600 text-brand-600' => $active,
                            'border-transparent text-zinc-500 hover:text-zinc-900' => ! $active,
                        ])>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main id="main" tabindex="-1" class="flex-1 focus:outline-none">
        @if ($errors->any())
            <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6">
                <div class="rounded-lg bg-red-50 px-4 py-3 text-red-700" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-zinc-900 text-sm text-zinc-400">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-6 sm:px-6">
            <x-logo class="text-white" />
            <p>&copy; {{ now()->year }} {{ config('app.name') }}. Prices in South African Rand, VAT included.</p>
        </div>
    </footer>
</body>
</html>
