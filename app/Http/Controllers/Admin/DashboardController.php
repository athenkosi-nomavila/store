<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // [label, value, icon, tone]. Counts include hidden products, since
        // the admin manages those too.
        return view('admin.dashboard', [
            'stats' => [
                ['Total products', Product::count(), 'box', 'brand'],
                ['Active products', Product::active()->count(), 'grid', 'green'],
                ['Out of stock', Product::where('quantity', 0)->count(), 'alert', 'red'],
                ['Categories', Category::count(), 'tag', 'amber'],
            ],
        ]);
    }
}
