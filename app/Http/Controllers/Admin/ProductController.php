<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /** Uploaded pictures get this prefix, so seeded pictures are never deleted. */
    private const UPLOAD_PREFIX = 'upload-';

    public function index(): View
    {
        return view('admin.products.index', [
            // Every product, hidden ones included.
            'products' => Product::with('category')->newest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = new Product($request->safe()->except('image'));
        $product->slug = $this->uniqueSlug($product->name);

        if ($request->hasFile('image')) {
            $product->image = $this->storeImage($request->file('image'));
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('status', "{$product->name} was added.");
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        // The slug (the product's web address) stays the same, so links keep working.
        $product->fill($request->safe()->except('image'));

        if ($request->hasFile('image')) {
            $old = $product->image;
            $product->image = $this->storeImage($request->file('image'));
            $this->deleteImage($old);
        }

        $product->save();

        return redirect()->route('admin.products.index')->with('status', "{$product->name} was saved.");
    }

    public function confirmDelete(Product $product): View
    {
        return view('admin.products.delete', ['product' => $product]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        $this->deleteImage($product->image);

        return redirect()->route('admin.products.index')->with('status', "{$product->name} was deleted.");
    }

    /**
     * Saves the picture under a random name with an extension worked out from
     * its contents, so the uploader controls neither the name nor the type.
     */
    private function storeImage(UploadedFile $file): string
    {
        $name = self::UPLOAD_PREFIX.Str::random(40).'.'.$file->extension();

        Storage::disk('product_images')->putFileAs('', $file, $name);

        return $name;
    }

    private function deleteImage(?string $image): void
    {
        if ($image && str_starts_with($image, self::UPLOAD_PREFIX)) {
            Storage::disk('product_images')->delete(basename($image));
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;

        for ($i = 2; Product::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
