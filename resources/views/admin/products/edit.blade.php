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
@endsection
