@extends('layouts.admin')

@section('title', 'Delete product')

@section('content')
    {{-- A confirm page instead of a pop-up, so deleting works without JavaScript. --}}
    <div class="max-w-xl rounded-xl border border-zinc-200 bg-white p-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                <x-product-image :product="$product" />
            </div>
            <div>
                <p class="font-semibold">{{ $product->name }}</p>
                <p class="text-sm text-zinc-500">{{ $product->sku }} · {{ $product->formatted_price }}</p>
            </div>
        </div>

        <p class="mt-6 text-zinc-700">Delete this product? It will disappear from the store straight away and can't be brought back. To hide it for now instead, untick "Show on the store" on its edit page.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-full bg-red-600 px-5 font-semibold text-white hover:bg-red-700">
                    <x-icon name="trash" size="18" /> Yes, delete it
                </button>
            </form>
            <a href="{{ route('admin.products.index') }}" class="inline-flex h-11 items-center rounded-full border border-zinc-300 px-5 font-semibold hover:border-zinc-400">Cancel</a>
        </div>
    </div>
@endsection
