@extends('layouts.app')

@section('title', 'Everyday essentials, thoughtfully chosen')

@section('content')
    {{-- Promotional banner --}}
    <section class="hero">
        <div class="container hero-inner">
            <div class="hero-copy">
                <p class="eyebrow">New season</p>
                <h1 class="hero-title">Upgrade your everyday</h1>
                <p class="hero-text">Audio, wearables and home pieces made to be used and loved.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg">Shop now</a>
            </div>
            <img class="hero-img" src="{{ asset('images/hero-headphones.jpg') }}" alt="" width="800" height="800">
        </div>
    </section>

    {{-- Categories --}}
    <section class="section" aria-labelledby="categories-heading">
        <div class="container">
            <h2 id="categories-heading" class="section-title">Shop by category</h2>
            <ul class="category-grid">
                @foreach ($featuredCategories as $category)
                    <li>
                        <a href="{{ route('shop.category', $category) }}" class="category-tile">
                            <span class="category-tile-name">{{ $category->name }}</span>
                            <span class="category-tile-count">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Latest products --}}
    <section class="section" aria-labelledby="latest-heading">
        <div class="container">
            <div class="section-head">
                <h2 id="latest-heading" class="section-title">Latest arrivals</h2>
                <a href="{{ route('shop.index') }}">View all</a>
            </div>

            @if ($products->isEmpty())
                <div class="empty-state">
                    <h3>New stock is on its way</h3>
                    <p>Check back soon for fresh arrivals.</p>
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
@endsection
