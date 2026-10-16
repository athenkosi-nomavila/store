@extends('layouts.app')

@php
    // One template serves /shop, /search and /category/{slug}.
    $heading = match (true) {
        $mode === 'search' && $query !== '' => 'Results for "'.$query.'"',
        $mode === 'search' => 'Search',
        $activeCategory !== null => $activeCategory->name,
        default => 'All products',
    };
    $formAction = match ($mode) {
        'search' => route('shop.search'),
        default => route('shop.index'),
    };
@endphp

@section('title', $heading)

@section('content')
    <div class="container listing">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="{{ route('home') }}">Home</a></li>
                @if ($mode === 'category')
                    <li><a href="{{ route('shop.index') }}">Shop</a></li>
                    <li aria-current="page">{{ $activeCategory->name }}</li>
                @elseif ($mode === 'search')
                    <li aria-current="page">Search</li>
                @else
                    <li aria-current="page">Shop</li>
                @endif
            </ol>
        </nav>

        <header class="listing-head">
            <h1 class="listing-title">{{ $heading }}</h1>
            @if ($mode === 'category' && $activeCategory->description)
                <p class="listing-description">{{ $activeCategory->description }}</p>
            @endif
            <p class="listing-count" role="status">
                {{ $products->total() }} {{ Str::plural('product', $products->total()) }} found
            </p>
        </header>

        <form class="toolbar" action="{{ $formAction }}" method="GET" data-filters>
            @if ($mode === 'search')
                <input type="hidden" name="q" value="{{ $query }}">
            @endif

            <button type="button" class="btn btn-outline filter-toggle" aria-expanded="false" aria-controls="filter-panel" data-filter-toggle>
                <x-icon name="filter" size="18" /> Filter and sort
            </button>

            <div class="filter-panel" id="filter-panel">
                <div class="field">
                    <label for="filter-category">Category</label>
                    <select id="filter-category" name="category" data-auto-submit>
                        <option value="">All categories</option>
                        @foreach ($navCategories as $navCategory)
                            <option value="{{ $navCategory->slug }}" @selected($activeCategory?->is($navCategory))>{{ $navCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="filter-sort">Sort by</label>
                    <select id="filter-sort" name="sort" data-auto-submit>
                        @foreach ($sorts as $value => $label)
                            <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary filter-apply">Apply</button>
            </div>
        </form>

        @if ($products->isEmpty())
            <div class="empty-state">
                <span class="empty-state-icon"><x-icon name="search" size="40" /></span>
                @if ($query !== '')
                    <h2>No products match "{{ $query }}"</h2>
                    <ul class="empty-tips">
                        <li>Check the spelling</li>
                        <li>Try a broader or different term</li>
                        <li>Browse one of our popular categories</li>
                    </ul>
                @else
                    <h2>Nothing here yet</h2>
                    <p>There are no products in this selection right now.</p>
                @endif
                <ul class="chip-list">
                    @foreach ($navCategories as $navCategory)
                        <li><a href="{{ route('shop.category', $navCategory) }}" class="chip">{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            {{ $products->links() }}
        @endif
    </div>
@endsection
