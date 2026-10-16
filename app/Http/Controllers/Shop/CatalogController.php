<?php

namespace App\Http\Controllers\Shop;

use App\Catalog\Catalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductListingRequest;
use App\Models\Category;
use Illuminate\View\View;

/**
 * Product listings: all products (/shop), search results (/search) and a
 * single category (/category/{slug}). All three render shop.search.
 */
class CatalogController extends Controller
{
    private const PER_PAGE = 12;

    public function __construct(private readonly Catalog $catalog) {}

    public function index(ProductListingRequest $request): View
    {
        return $this->listing($request, mode: 'shop');
    }

    public function search(ProductListingRequest $request): View
    {
        return $this->listing($request, mode: 'search');
    }

    public function category(ProductListingRequest $request, string $slug): View
    {
        $category = $this->catalog->category($slug) ?? abort(404);

        return $this->listing($request, mode: 'category', category: $category);
    }

    private function listing(ProductListingRequest $request, string $mode, ?Category $category = null): View
    {
        $query = $request->term();
        $sort = $request->sort();
        $category ??= $this->catalog->category((string) $request->validated('category'));

        $products = $this->catalog->find($category, $query, $sort)
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
