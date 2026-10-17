@props(['product'])
@if ($product->is_active)
    <x-availability-badge :product="$product" />
@else
    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-zinc-500">
        <x-icon name="eye-off" size="16" /> Hidden
    </span>
@endif
