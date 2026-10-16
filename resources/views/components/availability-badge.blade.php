@props(['product'])
<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 text-sm font-semibold',
    'text-green-700' => $product->stock_status === 'in',
    'text-amber-700' => $product->stock_status === 'low',
    'text-red-700' => $product->stock_status === 'out',
]) }}>
    <span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>{{ $product->stock_label }}
</span>
