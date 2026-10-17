@extends('layouts.app')

@section('title', 'Everyday essentials, thoughtfully chosen')

@section('content')
    {{-- Promotional banner --}}
    <section class="bg-gradient-to-r from-teal-100 to-stone-100">
        <div class="mx-auto grid max-w-6xl items-center gap-6 px-4 py-10 sm:px-6 md:gap-8 md:py-12 md:min-h-[480px] md:grid-cols-2">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-brand-600">New season</p>
                <h1 class="text-4xl font-bold tracking-tight md:text-6xl">Upgrade your everyday</h1>
                <p class="mb-6 mt-4 max-w-md text-lg text-zinc-600">Audio, wearables and home pieces made to be used and loved.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex h-12 items-center rounded-full bg-brand-600 px-7 font-semibold text-white hover:bg-brand-700">Shop now</a>
            </div>
            <img src="{{ asset('images/hero-headphones.jpg') }}" alt="" width="800" height="800"
                 class="order-first w-40 justify-self-center rounded-full shadow-lg sm:w-56 md:order-none md:w-full md:max-w-sm">
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        {{-- Categories --}}
        <section class="py-12" aria-labelledby="categories-heading">
            <h2 id="categories-heading" class="mb-5 text-2xl font-bold">Shop by category</h2>
            <ul class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-5">
                @foreach ($featuredCategories as $category)
                    <li>
                        <a href="{{ route('shop.category', $category) }}" class="block h-full rounded-xl border border-zinc-200 bg-white px-5 py-4 hover:border-brand-600">
                            <span class="block font-semibold">{{ $category->name }}</span>
                            <span class="text-sm text-zinc-500">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Latest products --}}
        <section class="pb-12" aria-labelledby="latest-heading">
            <div class="mb-5 flex items-baseline justify-between">
                <h2 id="latest-heading" class="text-2xl font-bold">Latest arrivals</h2>
                <a href="{{ route('shop.index') }}" class="font-medium text-brand-600 hover:text-brand-700">View all</a>
            </div>

            @if ($products->isEmpty())
                <div class="mx-auto max-w-md rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center">
                    <h3 class="text-xl font-bold">New stock is on its way</h3>
                    <p class="mt-2 text-zinc-500">Check back soon for fresh arrivals.</p>
                </div>
            @else
                <x-product-grid>
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </x-product-grid>
            @endif
        </section>
    </div>
@endsection
