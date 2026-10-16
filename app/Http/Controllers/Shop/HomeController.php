<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredCategories = Category::withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->get();

        $products = Product::active()
            ->with('category')
            ->latest()
            ->orderByDesc('id')
            ->take(8)
            ->get();

        return view('shop.home', compact('featuredCategories', 'products'));
    }
}
