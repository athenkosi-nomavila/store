@props(['product'])
<article @class(['product-card', 'is-sold-out' => ! $product->isInStock()])>
    <a href="{{ route('products.show', $product) }}" class="product-card-link">
        <div class="product-card-media">
            <x-product-image :product="$product" />
            <x-availability-badge :product="$product" class="product-card-badge" />
        </div>
        <div class="product-card-body">
            <p class="product-card-category">{{ $product->category->name }}</p>
            <h3 class="product-card-title">{{ $product->name }}</h3>
            <p class="product-card-price">{{ $product->formatted_price }}</p>
            <span class="btn btn-block {{ $product->isInStock() ? 'btn-outline' : 'btn-disabled' }}" aria-hidden="true">
                {{ $product->isInStock() ? 'View details' : 'Sold out' }}
            </span>
        </div>
    </a>
</article>
