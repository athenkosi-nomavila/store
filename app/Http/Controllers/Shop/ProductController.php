<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        // Hidden products behave exactly like missing ones.
        abort_unless($product->is_active, 404);

        $product->load('category');

        $related = Product::active()
            ->with('category')
            ->whereBelongsTo($product->category)
            ->whereKeyNot($product->getKey())
            ->latest()
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}
