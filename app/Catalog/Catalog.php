<?php

namespace App\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-only access to the storefront's products and categories.
 *
 * Every product lookup returns active products only, so hidden products
 * behave exactly like missing ones. All queries go through Eloquent, which
 * binds every value as a parameter (no SQL is built from user input).
 */
class Catalog
{
    /** @var Collection<string, Category>|null keyed by slug */
    private ?Collection $categories = null;

    /**
     * Every category, A to Z, with a count of its active products.
     *
     * @return Collection<string, Category>
     */
    public function categories(): Collection
    {
        return $this->categories ??= Category::query()
            ->withCount(['products' => fn (Builder $q) => $q->active()])
            ->orderBy('name')
            ->get()
            ->keyBy('slug');
    }

    public function category(string $slug): ?Category
    {
        return $this->categories()->get($slug);
    }

    public function product(string $slug): ?Product
    {
        return $this->products()->where('slug', $slug)->first();
    }

    /** @return Collection<int, Product> */
    public function latest(int $limit): Collection
    {
        return $this->products()->newest()->limit($limit)->get();
    }

    /** @return Collection<int, Product> */
    public function related(Product $product, int $limit): Collection
    {
        return $this->products()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->newest()
            ->limit($limit)
            ->get();
    }

    /**
     * Filter by category and search term, then sort.
     *
     * @return Builder<Product>
     */
    public function find(?Category $category = null, string $term = '', string $sort = 'newest'): Builder
    {
        // % and _ are LIKE wildcards; escape them (and the escape character)
        // so a search for "100%" means exactly that. The term is still bound
        // as a parameter, never written into the SQL.
        $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term).'%';

        $query = $this->products()
            ->when($category, fn (Builder $q) => $q->where('category_id', $category->getKey()))
            ->when($term !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->whereRaw("name like ? escape '!'", [$pattern])
                ->orWhereRaw("description like ? escape '!'", [$pattern])));

        return match ($sort) {
            'price_asc' => $query->orderBy('price')->newest(),
            'price_desc' => $query->orderByDesc('price')->newest(),
            'name' => $query->orderBy('name'),
            default => $query->newest(),
        };
    }

    /** @return Builder<Product> */
    private function products(): Builder
    {
        return Product::query()->active()->with('category');
    }
}
