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
            <div @class(['product-main-image', 'is-sold-out' => ! $product->isInStock()])>
                <x-product-image :product="$product" eager />
            </div>

            <div class="product-info">
                <p class="product-category">{{ $product->category->name }}</p>
                <h1 class="product-title">{{ $product->name }}</h1>
                <p class="product-price">{{ $product->formatted_price }}</p>
                <x-availability-badge :product="$product" />

                {{-- Escaped by Blade; line breaks kept by CSS (white-space: pre-line). --}}
                <p class="product-description">{{ $product->description }}</p>

                <table class="details-table">
                    <tbody>
                        <tr><th scope="row">Category</th><td>{{ $product->category->name }}</td></tr>
                        <tr><th scope="row">SKU</th><td>{{ $product->sku }}</td></tr>
                        <tr><th scope="row">Availability</th><td>{{ $product->stock_label }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="section" aria-labelledby="related-heading">
                <h2 id="related-heading" class="section-title">More from {{ $product->category->name }}</h2>
                <div class="product-grid">
                    @foreach ($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
