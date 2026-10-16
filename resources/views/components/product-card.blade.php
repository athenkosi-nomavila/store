@props(['product'])
<article class="rounded-xl bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md motion-reduce:transition-none motion-reduce:hover:translate-y-0">
    <a href="{{ route('products.show', $product) }}" class="flex h-full flex-col rounded-xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
        <div class="aspect-square overflow-hidden rounded-t-xl bg-stone-100">
            <x-product-image :product="$product" />
        </div>
        <div class="flex flex-1 flex-col items-start p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ $product->category->name }}</p>
            <h3 class="mb-2 mt-1 line-clamp-2 font-semibold leading-snug">{{ $product->name }}</h3>
            <p class="mb-2 mt-auto font-bold text-brand-600">{{ $product->formatted_price }}</p>
            <x-availability-badge :product="$product" />
        </div>
    </a>
</article>
