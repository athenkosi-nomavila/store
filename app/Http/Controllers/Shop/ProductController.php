<?php

namespace App\Http\Controllers\Shop;

use App\Catalog\Catalog;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Catalog $catalog, string $slug): View
    {
        // Missing and hidden products both return 404.
        $product = $catalog->product($slug) ?? abort(404);

        return view('shop.product', [
            'product' => $product,
            'related' => $catalog->related($product, 4),
        ]);
    }
}
