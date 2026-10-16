<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'Everyday essentials, thoughtfully chosen.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
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

            <nav class="main-nav" aria-label="Main">
                <ul class="nav-list">
                    <li><a href="{{ route('home') }}" @class(['nav-link', 'is-active' => request()->routeIs('home')])>Home</a></li>
                    <li><a href="{{ route('shop.index') }}" @class(['nav-link', 'is-active' => request()->routeIs('shop.index', 'shop.search')])>Shop</a></li>
                    <li class="nav-dropdown" data-dropdown>
                        <button type="button" @class(['nav-link', 'is-active' => request()->routeIs('shop.category')]) aria-expanded="false" aria-controls="categories-menu" data-dropdown-toggle>
                            Categories <x-icon name="chevron-down" size="16" />
                        </button>
                        <ul class="dropdown-menu" id="categories-menu" hidden>
                            @foreach ($navCategories as $navCategory)
                                <li><a href="{{ route('shop.category', $navCategory) }}">{{ $navCategory->name }}</a></li>
                            @endforeach
                            <li class="dropdown-all"><a href="{{ route('shop.index') }}">All products</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ route('about') }}" @class(['nav-link', 'is-active' => request()->routeIs('about')])>About</a></li>
                    <li><a href="{{ route('contact') }}" @class(['nav-link', 'is-active' => request()->routeIs('contact')])>Contact</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="{{ route('shop.index') }}" class="icon-button cart-button" aria-label="Cart, 0 items" data-cart-link>
                    <x-icon name="cart" size="22" />
                    <span class="cart-count" data-cart-count>0</span>
                </a>
                <button type="button" class="icon-button menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-drawer" data-drawer-open>
                    <x-icon name="menu" size="24" />
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile navigation drawer --}}
    <div class="drawer" id="mobile-drawer" hidden data-drawer>
        <div class="drawer-backdrop" data-drawer-close></div>
        <div class="drawer-panel" role="dialog" aria-modal="true" aria-label="Menu">
            <div class="drawer-head">
                <x-logo />
                <button type="button" class="icon-button" aria-label="Close menu" data-drawer-close>
                    <x-icon name="close" size="24" />
                </button>
            </div>
            <nav aria-label="Mobile">
                <ul class="drawer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('shop.index') }}">Shop all products</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
                <p class="drawer-heading">Categories</p>
                <ul class="drawer-links drawer-categories">
                    @foreach ($navCategories as $navCategory)
                        <li>
                            <a href="{{ route('shop.category', $navCategory) }}">
                                <x-category-icon :slug="$navCategory->slug" size="20" />
                                {{ $navCategory->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </div>

    <main id="main" tabindex="-1">
        @if (session('status'))
            <div class="container"><p class="flash flash-success" role="status">{{ session('status') }}</p></div>
        @endif
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
        <div class="container footer-grid">
            <div class="footer-brand">
                <x-logo class="logo-light" />
                <p>Everyday essentials, thoughtfully chosen. Store brings together audio, home and lifestyle goods we would happily use ourselves.</p>
            </div>
            <div>
                <h2 class="footer-heading">Shop</h2>
                <ul class="footer-links">
                    <li><a href="{{ route('shop.index') }}">All products</a></li>
                    @foreach ($navCategories->take(4) as $navCategory)
                        <li><a href="{{ route('shop.category', $navCategory) }}">{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2 class="footer-heading">Help</h2>
                <ul class="footer-links">
                    <li><a href="{{ route('contact') }}">Contact us</a></li>
                    <li><a href="{{ route('about') }}#delivery">Delivery</a></li>
                    <li><a href="{{ route('about') }}#returns">Returns</a></li>
                    <li><a href="{{ route('about') }}">About Store</a></li>
                </ul>
            </div>
            <div>
                <h2 class="footer-heading">Stay in the loop</h2>
                <p>New arrivals and offers, once a month.</p>
                <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <label for="newsletter-email" class="sr-only">Email address</label>
                    <input id="newsletter-email" type="email" name="email" placeholder="you@example.com" required maxlength="255" autocomplete="email">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Prices include VAT and are shown in South African Rand.</p>
        </div>
    </footer>
</body>
</html>
