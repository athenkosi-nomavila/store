{{-- Shared add/edit product form. Blade escapes every value, and @csrf adds the CSRF token. --}}
@php
    $input = 'h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30 aria-[invalid=true]:border-red-500';
@endphp

<form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($product)
        @method('PUT')
    @endif

    <div class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 lg:col-span-2">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Name</label>
            <input id="name" @error('name') aria-invalid="true" @enderror name="name" value="{{ old('name', $product?->name) }}" required maxlength="150" class="{{ $input }}">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <p class="mb-1 block text-sm font-medium">SKU</p>
                <p class="flex h-11 items-center rounded-lg border border-zinc-200 bg-zinc-50 px-3 {{ $product ? '' : 'text-zinc-500' }}">{{ $product?->sku ?? 'Made when you add the product' }}</p>
            </div>
            <div>
                <label for="category_id" class="mb-1 block text-sm font-medium">Category</label>
                <select id="category_id" @error('category_id') aria-invalid="true" @enderror name="category_id" required class="{{ $input }}">
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="price" class="mb-1 block text-sm font-medium">Price (R)</label>
                <input id="price" @error('price') aria-invalid="true" @enderror type="number" name="price" value="{{ old('price', $product?->price) }}" required min="0" max="99999999.99" step="0.01" inputmode="decimal" class="{{ $input }}">
            </div>
            <div>
                <label for="quantity" class="mb-1 block text-sm font-medium">Quantity in stock</label>
                <input id="quantity" @error('quantity') aria-invalid="true" @enderror type="number" name="quantity" value="{{ old('quantity', $product?->quantity ?? 0) }}" required min="0" step="1" aria-describedby="quantity-help" class="{{ $input }}">
                <p id="quantity-help" class="mt-1 text-xs text-zinc-500">0 shows the product as out of stock.</p>
            </div>
        </div>

        <div>
            <label for="description" class="mb-1 block text-sm font-medium">Description</label>
            <textarea id="description" @error('description') aria-invalid="true" @enderror name="description" rows="6" maxlength="5000"
                      class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/30 aria-[invalid=true]:border-red-500">{{ old('description', $product?->description) }}</textarea>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <h2 class="mb-3 text-sm font-medium">Image</h2>
            <div class="mb-4 aspect-square overflow-hidden rounded-lg bg-stone-100">
                @if ($product)
                    <x-product-image :product="$product" />
                @else
                    <div class="flex h-full items-center justify-center text-sm text-zinc-400">No image yet</div>
                @endif
            </div>
            <label for="image" class="sr-only">Upload an image</label>
            <input id="image" @error('image') aria-invalid="true" @enderror type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-describedby="image-help"
                   class="block w-full text-sm text-zinc-600 file:mr-3 file:h-10 file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:font-semibold file:text-brand-700 hover:file:bg-brand-50/70">
            <p id="image-help" class="mt-2 text-xs text-zinc-500">JPG, PNG or WebP, up to 2 MB.</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6">
            <label class="flex items-start gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))
                       class="mt-0.5 h-5 w-5 rounded border-zinc-300 accent-brand-600">
                <span>
                    <span class="block text-sm font-medium">Show on the store</span>
                    <span class="block text-xs text-zinc-500">Untick to hide this product from customers.</span>
                </span>
            </label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="h-11 flex-1 rounded-full bg-brand-600 px-5 font-semibold text-white hover:bg-brand-700">
                {{ $product ? 'Save changes' : 'Add product' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="inline-flex h-11 items-center rounded-full border border-zinc-300 bg-white px-5 font-semibold hover:border-zinc-400">Cancel</a>
        </div>
    </div>
</form>
