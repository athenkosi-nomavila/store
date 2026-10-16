<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductListingRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * Product listings: all products (/shop), search results (/search) and a
 * single category (/category/{slug}). All three render shop.search.
 */
class CatalogController extends Controller
{
    private const PER_PAGE = 12;

    public function index(ProductListingRequest $request): View
    {
        return $this->listing($request, mode: 'shop');
    }

    public function search(ProductListingRequest $request): View
    {
        return $this->listing($request, mode: 'search');
    }

    public function category(ProductListingRequest $request, Category $category): View
    {
        return $this->listing($request, mode: 'category', category: $category);
    }

    private function listing(ProductListingRequest $request, string $mode, ?Category $category = null): View
    {
        $query = $request->term();
        $sort = $request->sort();
        $category ??= $request->validated('category')
            ? Category::where('slug', $request->validated('category'))->first()
            : null;

        $products = Product::active()
            ->with('category')
            ->when($category, fn (Builder $q) => $q->whereBelongsTo($category))
            ->when($query !== '', fn (Builder $q) => $q->search($query))
            ->sortBy($sort)
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('shop.search', [
            'mode' => $mode,
            'products' => $products,
            'query' => $query,
            'sort' => $sort,
            'sorts' => ProductListingRequest::SORTS,
            'activeCategory' => $category,
        ]);
    }
}
