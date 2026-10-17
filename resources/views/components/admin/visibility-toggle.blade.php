@props(['product'])
{{-- A plain form, so it works without JavaScript and carries the CSRF token. --}}
<form method="POST" action="{{ route('admin.products.visibility', $product) }}">
    @csrf
    @method('PATCH')
    <input type="hidden" name="is_active" value="{{ $product->is_active ? 0 : 1 }}">
    <button type="submit" title="{{ $product->is_active ? 'Hide from store' : 'Show on store' }}"
            class="flex h-10 w-10 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900">
        <x-icon :name="$product->is_active ? 'eye-off' : 'eye'" size="18" />
        <span class="sr-only">{{ $product->is_active ? 'Hide' : 'Show' }} {{ $product->name }}</span>
    </button>
</form>
