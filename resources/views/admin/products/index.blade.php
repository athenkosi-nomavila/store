@extends('layouts.admin')

@section('title', 'Products')

@section('actions')
    <a href="{{ route('admin.products.create') }}" class="inline-flex h-11 items-center gap-2 rounded-full bg-brand-600 px-5 text-sm font-semibold text-white hover:bg-brand-700">
        <x-icon name="plus" size="18" /> Add product
    </a>
@endsection

@section('content')
    @if ($products->isEmpty())
        <div class="rounded-xl border border-zinc-200 bg-white px-6 py-12 text-center">
            <p class="font-semibold">No products yet</p>
            <p class="mt-1 text-sm text-zinc-500">Add your first product to show it on the store.</p>
        </div>
    @else
        {{-- relative: keeps the screen-reader-only labels inside the scroll area. --}}
        <div class="relative overflow-x-auto rounded-xl border border-zinc-200 bg-white">
            <table class="w-full min-w-[44rem] text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 font-semibold">Product</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Category</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">Price</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold">Quantity</th>
                        <th scope="col" class="px-4 py-3 font-semibold">Status</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                                        <x-product-image :product="$product" />
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $product->name }}</p>
                                        <p class="text-xs text-zinc-500">{{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $product->category->name }}</td>
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap">{{ $product->formatted_price }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $product->quantity }}</td>
                            <td class="px-4 py-3">
                                @if ($product->is_active)
                                    <x-availability-badge :product="$product" />
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-zinc-500">
                                        <x-icon name="eye-off" size="16" /> Hidden
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.products.edit', $product) }}" title="Edit"
                                       class="flex h-10 w-10 items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900">
                                        <x-icon name="pencil" size="18" /><span class="sr-only">Edit {{ $product->name }}</span>
                                    </a>
                                    {{-- Deleting is wired up in the product management step. --}}
                                    <button type="button" disabled title="Delete"
                                            class="flex h-10 w-10 items-center justify-center rounded-full text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50">
                                        <x-icon name="trash" size="18" /><span class="sr-only">Delete {{ $product->name }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    @endif
@endsection
