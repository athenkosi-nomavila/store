@extends('layouts.app')

@php
    // One template serves /shop, /search and /category/{slug}.
    $heading = match (true) {
        $mode === 'search' && $query !== '' => 'Results for "'.$query.'"',
        $mode === 'search' => 'Search',
        $activeCategory !== null => $activeCategory->name,
        default => 'All products',
    };
    $formAction = $mode === 'search' ? route('shop.search') : route('shop.index');
    $crumbs = $mode === 'category' ? ['Home' => route('home'), 'Shop' => route('shop.index')] : ['Home' => route('home')];
    $selectClass = 'h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30 pr-10';
@endphp

@section('title', $heading)

@section('content')
    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6">
        <x-breadcrumb :links="$crumbs" :current="match ($mode) { 'category' => $activeCategory->name, 'search' => 'Search', default => 'Shop' }" />

        <header class="mb-5">
            <h1 class="break-words text-3xl font-bold md:text-4xl">{{ $heading }}</h1>
            @if ($mode === 'category' && $activeCategory->description)
                <p class="mt-1 max-w-2xl text-zinc-500">{{ $activeCategory->description }}</p>
            @endif
            <p class="mt-1 text-zinc-500" role="status">{{ $products->total() }} {{ Str::plural('product', $products->total()) }} found</p>
        </header>

        <form action="{{ $formAction }}" method="GET" class="mb-6 flex flex-wrap items-end gap-3">
            @if ($mode === 'search')
                <input type="hidden" name="q" value="{{ $query }}">
            @endif
            <div class="min-w-40 flex-1 md:max-w-56">
                <label for="filter-category" class="mb-1 block text-sm font-semibold text-zinc-500">Category</label>
                <select id="filter-category" name="category" class="{{ $selectClass }}">
                    <option value="">All categories</option>
                    @foreach ($navCategories as $navCategory)
                        <option value="{{ $navCategory->slug }}" @selected($activeCategory?->is($navCategory))>{{ $navCategory->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-40 flex-1 md:max-w-56">
                <label for="filter-sort" class="mb-1 block text-sm font-semibold text-zinc-500">Sort by</label>
                <select id="filter-sort" name="sort" class="{{ $selectClass }}">
                    @foreach ($sorts as $value => $label)
                        <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="h-11 rounded-full bg-brand-600 px-6 font-semibold text-white hover:bg-brand-700">Apply</button>
        </form>

        @if ($products->isEmpty())
            <div class="mx-auto max-w-md rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center">
                @if ($query !== '')
                    <h2 class="break-words text-xl font-bold">No products match "{{ $query }}"</h2>
                    <p class="mt-2 text-zinc-500">Check the spelling or try a broader term.</p>
                @else
                    <h2 class="text-xl font-bold">Nothing here yet</h2>
                    <p class="mt-2 text-zinc-500">There are no products in this selection right now.</p>
                @endif
                <a href="{{ route('shop.index') }}" class="mt-5 inline-flex h-11 items-center rounded-full bg-brand-600 px-6 font-semibold text-white hover:bg-brand-700">Browse all products</a>
            </div>
        @else
            <x-product-grid>
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </x-product-grid>

            {{ $products->links() }}
        @endif
    </div>
@endsection
