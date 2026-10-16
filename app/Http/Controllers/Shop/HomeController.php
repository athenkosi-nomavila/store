<?php

namespace App\Http\Controllers\Shop;

use App\Catalog\Catalog;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Catalog $catalog): View
    {
        return view('shop.home', [
            'featuredCategories' => $catalog->categories(),
            'products' => $catalog->latest(8),
        ]);
    }
}
