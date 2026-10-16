@extends('layouts.admin')

@section('title', 'Edit product')

@section('actions')
    @if ($product->is_active)
        <a href="{{ route('products.show', $product) }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-full border border-zinc-300 bg-white px-5 text-sm font-semibold hover:border-zinc-400">
            <x-icon name="external" size="18" /> View on store
        </a>
    @endif
@endsection

@section('content')
    @include('admin.products._form', ['product' => $product])

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-red-200 bg-white p-6">
        <div>
            <h2 class="font-semibold">Delete this product</h2>
            <p class="text-sm text-zinc-500">It disappears from the store and can't be brought back.</p>
        </div>
        <a href="{{ route('admin.products.delete', $product) }}" class="inline-flex h-11 items-center gap-2 rounded-full border border-red-300 px-5 text-sm font-semibold text-red-600 hover:bg-red-50">
            <x-icon name="trash" size="18" /> Delete product
        </a>
    </div>
@endsection
