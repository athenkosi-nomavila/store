@extends('layouts.app')

@section('title', $product->name)
@section('description', Str::limit($product->description, 150))

@section('content')
    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6">
        <x-breadcrumb :links="['Home' => route('home'), $product->category->name => route('shop.category', $product->category)]" :current="$product->name" />

        <div class="grid items-start gap-8 md:grid-cols-2 md:gap-12">
            <div class="aspect-square overflow-hidden rounded-xl bg-stone-100">
                <x-product-image :product="$product" eager />
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ $product->category->name }}</p>
                <h1 class="mt-1 text-3xl font-bold md:text-4xl">{{ $product->name }}</h1>
                <p class="my-3 text-2xl font-bold text-brand-600">{{ $product->formatted_price }}</p>
                <x-availability-badge :product="$product" />

                {{-- Escaped by Blade; whitespace-pre-line keeps the line breaks. --}}
                <p class="my-6 whitespace-pre-line text-zinc-700">{{ $product->description }}</p>

                <dl class="divide-y divide-zinc-200 border-t border-zinc-200 text-sm">
                    @foreach (['Category' => $product->category->name, 'SKU' => $product->sku, 'Availability' => $product->stock_label] as $label => $value)
                        <div class="grid grid-cols-5 py-3">
                            <dt class="col-span-2 text-zinc-500">{{ $label }}</dt>
                            <dd class="col-span-3">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <section class="pt-12" aria-labelledby="related-heading">
                <h2 id="related-heading" class="mb-5 text-2xl font-bold">More from {{ $product->category->name }}</h2>
                <x-product-grid>
                    @foreach ($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </x-product-grid>
            </section>
        @endif
    </div>
@endsection
