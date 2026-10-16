@props(['product', 'eager' => false])
@if ($product->image_url)
    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="800" height="800"
         loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async" {{ $attributes }}>
@else
    {{-- Placeholder with the product initial when no image has been uploaded. --}}
    <svg viewBox="0 0 100 100" role="img" aria-label="{{ $product->name }}" {{ $attributes->merge(['class' => 'image-placeholder']) }}>
        <rect width="100" height="100" fill="#EFEBE5"/>
        <text x="50" y="50" dy=".35em" text-anchor="middle" font-family="Inter, sans-serif" font-size="38" font-weight="600" fill="#9A958E">{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}</text>
    </svg>
@endif
