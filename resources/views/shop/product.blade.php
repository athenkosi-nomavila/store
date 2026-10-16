@extends('layouts.app')

@section('title', $product->name)
@section('description', Str::limit($product->description ?? '', 150))

@section('content')
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop.category', $product->category) }}">{{ $product->category->name }}</a></li>
                <li aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="product-layout">
            <div class="product-gallery">
                <div @class(['product-main-image', 'is-sold-out' => ! $product->isInStock()])>
                    <x-product-image :product="$product" eager />
                </div>
            </div>

            <div class="product-info">
                <a href="{{ route('shop.category', $product->category) }}" class="tag">{{ $product->category->name }}</a>
                <h1 class="product-title">{{ $product->name }}</h1>
                <p class="product-price">{{ $product->formatted_price }}</p>
                <x-availability-badge :product="$product" class="badge-lg" />

                <p class="product-summary">{{ Str::of($product->description)->explode("\n")->first() }}</p>

                {{-- No checkout yet: the cart count is kept in the browser only. --}}
                <form class="buy-form" data-add-to-cart>
                    <div class="qty" data-qty>
                        <label for="quantity" class="qty-label">Quantity</label>
                        <div class="qty-control">
                            <button type="button" class="qty-btn" aria-label="Decrease quantity" data-qty-step="-1" @disabled(! $product->isInStock())><x-icon name="minus" size="18" /></button>
                            <input id="quantity" name="quantity" type="number" inputmode="numeric"
                                   value="1" min="1" max="{{ max($product->quantity, 1) }}" step="1" @disabled(! $product->isInStock())>
                            <button type="button" class="qty-btn" aria-label="Increase quantity" data-qty-step="1" @disabled(! $product->isInStock())><x-icon name="plus" size="18" /></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg buy-button" @disabled(! $product->isInStock())>
                        <x-icon name="cart" size="20" />
                        {{ $product->isInStock() ? 'Add to cart' : 'Out of stock' }}
                    </button>
                    <p class="buy-feedback" role="status" aria-live="polite" data-cart-feedback></p>
                </form>

                <ul class="perks">
                    <li><x-icon name="truck" /> Free delivery on orders over R 500</li>
                    <li><x-icon name="return" /> 30-day hassle-free returns</li>
                    <li><x-icon name="shield" /> Secure, encrypted payment</li>
                </ul>
            </div>
        </div>

        <div class="product-details">
            <section aria-labelledby="description-heading">
                <h2 id="description-heading" class="section-title-sm">Description</h2>
                {{-- Escaped first, then newlines turned into paragraphs by CSS (white-space: pre-line). --}}
                <p class="product-description">{{ $product->description }}</p>
            </section>
            <section aria-labelledby="details-heading">
                <h2 id="details-heading" class="section-title-sm">Product details</h2>
                <table class="details-table">
                    <tbody>
                        <tr><th scope="row">Category</th><td>{{ $product->category->name }}</td></tr>
                        <tr><th scope="row">SKU</th><td>{{ $product->sku }}</td></tr>
                        <tr><th scope="row">Availability</th><td>{{ $product->stock_label }}</td></tr>
                        <tr><th scope="row">Price</th><td>{{ $product->formatted_price }} incl. VAT</td></tr>
                    </tbody>
                </table>
            </section>
        </div>

        @if ($related->isNotEmpty())
            <section class="section related" aria-labelledby="related-heading">
                <div class="section-head">
                    <h2 id="related-heading" class="section-title">You may also like</h2>
                    <a href="{{ route('shop.category', $product->category) }}" class="link-arrow">More {{ $product->category->name }} <x-icon name="arrow-right" size="16" /></a>
                </div>
                <div class="product-grid">
                    @foreach ($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
