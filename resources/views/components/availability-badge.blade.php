@props(['product'])
<span {{ $attributes->class(['badge', 'badge-'.$product->stock_status]) }}>
    <span class="badge-dot" aria-hidden="true"></span>{{ $product->stock_label }}
</span>
