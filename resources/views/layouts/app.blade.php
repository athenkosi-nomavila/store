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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="container header-inner">
            <x-logo />

            <form class="search-form" action="{{ route('shop.search') }}" method="GET" role="search">
                <label for="site-search" class="sr-only">Search products</label>
                <input id="site-search" type="search" name="q" value="{{ is_string(request()->query('q')) ? request()->query('q') : '' }}"
                       placeholder="Search products..." maxlength="100" autocomplete="off">
                <button type="submit" class="search-button" aria-label="Search">
                    <x-icon name="search" />
                </button>
            </form>
        </div>

        @php($currentCategory = request()->route('category'))
        <nav class="container main-nav" aria-label="Main">
            <ul class="nav-list">
                <li><a href="{{ route('home') }}" @class(['nav-link', 'is-active' => request()->routeIs('home')])>Home</a></li>
                <li><a href="{{ route('shop.index') }}" @class(['nav-link', 'is-active' => request()->routeIs('shop.index', 'shop.search') && ! request()->filled('category')])>All products</a></li>
                @foreach ($navCategories as $navCategory)
                    <li><a href="{{ route('shop.category', $navCategory) }}" @class(['nav-link', 'is-active' => $currentCategory instanceof \App\Models\Category && $currentCategory->is($navCategory)])>{{ $navCategory->name }}</a></li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main id="main" tabindex="-1">
        @if ($errors->any())
            <div class="container">
                <div class="flash flash-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <x-logo class="logo-light" />
            <p>&copy; {{ now()->year }} {{ config('app.name') }}. Prices in South African Rand, VAT included.</p>
        </div>
    </footer>
</body>
</html>
