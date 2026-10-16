@extends('layouts.app')

@section('title', 'Everyday essentials, thoughtfully chosen')

@section('content')
    {{-- Promotional banner --}}
    <section class="hero" aria-roledescription="carousel" aria-label="Featured promotions" data-carousel>
        <div class="hero-track">
            <div class="hero-slide is-active" role="group" aria-roledescription="slide" aria-label="1 of 2" data-slide>
                <div class="container hero-inner">
                    <div class="hero-copy">
                        <p class="eyebrow">New season</p>
                        <h1 class="hero-title">Upgrade your everyday</h1>
                        <p class="hero-text">Audio, wearables and home pieces made to be used and loved, delivered free over R 500.</p>
                        <div class="hero-actions">
                            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg">Shop now <x-icon name="arrow-right" size="18" /></a>
                            <a href="#categories" class="btn btn-outline btn-lg">View categories</a>
                        </div>
                    </div>
                    <div class="hero-visual" aria-hidden="true">
                        <div class="hero-blob"></div>
                        <img class="hero-img hero-img-main" src="{{ asset('images/hero-headphones.jpg') }}" alt="" width="800" height="800">
                        <img class="hero-img hero-img-side" src="{{ asset('images/hero-watch.jpg') }}" alt="" width="800" height="800">
                    </div>
                </div>
            </div>
            <div class="hero-slide hero-slide-warm" role="group" aria-roledescription="slide" aria-label="2 of 2" data-slide hidden>
                <div class="container hero-inner">
                    <div class="hero-copy">
                        <p class="eyebrow">Home edit</p>
                        <p class="hero-title">Slow mornings, warm light</p>
                        <p class="hero-text">Lamps, linen and stoneware picked to make home feel calmer.</p>
                        <div class="hero-actions">
                            <a href="{{ route('shop.category', 'home-living') }}" class="btn btn-primary btn-lg">Shop home <x-icon name="arrow-right" size="18" /></a>
                            <a href="{{ route('shop.category', 'kitchen') }}" class="btn btn-outline btn-lg">Kitchen picks</a>
                        </div>
                    </div>
                    <div class="hero-visual" aria-hidden="true">
                        <div class="hero-blob"></div>
                        <img class="hero-img hero-img-main" src="{{ asset('images/hero-lamp.jpg') }}" alt="" width="800" height="800" loading="lazy">
                        <img class="hero-img hero-img-side" src="{{ asset('images/hero-mug.jpg') }}" alt="" width="800" height="800" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-dots" role="group" aria-label="Choose slide">
            <button type="button" class="hero-dot is-active" aria-label="Show slide 1" aria-current="true" data-slide-to="0"></button>
            <button type="button" class="hero-dot" aria-label="Show slide 2" data-slide-to="1"></button>
        </div>
    </section>

    {{-- Trust strip --}}
    <section class="trust-strip" aria-label="Why shop with us">
        <ul class="container trust-list">
            <li><x-icon name="truck" size="26" /><span><strong>Free delivery</strong> on orders over R 500</span></li>
            <li><x-icon name="shield" size="26" /><span><strong>Secure checkout</strong> with encrypted payments</span></li>
            <li><x-icon name="return" size="26" /><span><strong>30-day returns</strong> with no questions asked</span></li>
            <li><x-icon name="support" size="26" /><span><strong>Local support</strong> from our Cape Town team</span></li>
        </ul>
    </section>

    {{-- Categories --}}
    <section class="section" id="categories" aria-labelledby="categories-heading">
        <div class="container">
            <div class="section-head">
                <h2 id="categories-heading" class="section-title">Shop by category</h2>
                <a href="{{ route('shop.index') }}" class="link-arrow">View all <x-icon name="arrow-right" size="16" /></a>
            </div>
            <ul class="category-grid">
                @foreach ($featuredCategories as $category)
                    <li>
                        <a href="{{ route('shop.category', $category) }}" class="category-tile">
                            <span class="category-tile-icon"><x-category-icon :slug="$category->slug" /></span>
                            <span class="category-tile-name">{{ $category->name }}</span>
                            <span class="category-tile-count">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Latest products --}}
    <section class="section section-tinted" aria-labelledby="latest-heading">
        <div class="container">
            <div class="section-head">
                <h2 id="latest-heading" class="section-title">Latest arrivals</h2>
                <a href="{{ route('shop.index') }}" class="link-arrow">Shop all <x-icon name="arrow-right" size="16" /></a>
            </div>

            @if ($products->isEmpty())
                <div class="empty-state">
                    <span class="empty-state-icon"><x-icon name="box" size="40" /></span>
                    <h3>New stock is on its way</h3>
                    <p>We are restocking the shelves. Check back soon for fresh arrivals.</p>
                </div>
            @else
                <div class="product-grid">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Secondary promo --}}
    <section class="section" aria-label="Offers">
        <div class="container promo-grid">
            <a href="{{ route('shop.category', 'audio') }}" class="promo promo-dark">
                <span class="eyebrow">Limited time</span>
                <span class="promo-title">Up to 30% off audio</span>
                <span class="promo-link">Shop audio <x-icon name="arrow-right" size="16" /></span>
                <img src="{{ asset('images/promo-speaker.jpg') }}" alt="" class="promo-img" width="800" height="800" loading="lazy">
            </a>
            <a href="{{ route('shop.category', 'home-living') }}" class="promo promo-light">
                <span class="eyebrow">Just landed</span>
                <span class="promo-title">New in home</span>
                <span class="promo-link">Explore home <x-icon name="arrow-right" size="16" /></span>
                <img src="{{ asset('images/promo-vase.jpg') }}" alt="" class="promo-img" width="800" height="800" loading="lazy">
            </a>
        </div>
    </section>
@endsection
