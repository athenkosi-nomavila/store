<?php

namespace App\Catalog;

use Illuminate\Support\Collection;

/**
 * Read-only access to the sample catalogue in config/catalog.php.
 *
 * Every lookup returns active products only, so hidden products behave
 * exactly like missing ones.
 */
class Catalog
{
    /** @var Collection<string, Category> keyed by slug */
    private Collection $categories;

    /** @var Collection<int, Product> active products, newest first */
    private Collection $products;

    public function __construct(array $data)
    {
        $this->categories = collect($data['categories'] ?? [])
            ->map(fn (array $c, string $slug) => new Category($slug, $c['name'], $c['description'] ?? null))
            ->sortBy('name')
            ->keyBy('slug');

        $this->products = collect($data['products'] ?? [])
            ->values()
            ->map(fn (array $p, int $i) => new Product(
                slug: $p['slug'],
                sku: $p['sku'],
                name: $p['name'],
                category: $this->categories[$p['category']],
                price: (float) $p['price'],
                quantity: (int) $p['quantity'],
                image: $p['image'] ?? null,
                description: $p['description'] ?? '',
                is_active: $p['active'] ?? true,
                position: $i,
            ))
            ->filter(fn (Product $p) => $p->is_active)
            ->values();

        foreach ($this->categories as $category) {
            $category->products_count = $this->products->filter(fn (Product $p) => $p->category->is($category))->count();
        }
    }

    /** @return Collection<string, Category> */
    public function categories(): Collection
    {
        return $this->categories;
    }

    public function category(string $slug): ?Category
    {
        return $this->categories->get($slug);
    }

    public function product(string $slug): ?Product
    {
        return $this->products->first(fn (Product $p) => $p->slug === $slug);
    }

    /** @return Collection<int, Product> */
    public function latest(int $limit): Collection
    {
        return $this->products->take($limit);
    }

    /** @return Collection<int, Product> */
    public function related(Product $product, int $limit): Collection
    {
        return $this->products
            ->filter(fn (Product $p) => $p->category->is($product->category) && $p->slug !== $product->slug)
            ->take($limit)
            ->values();
    }

    /**
     * Filter by category and search term, then sort.
     *
     * @return Collection<int, Product>
     */
    public function find(?Category $category = null, string $term = '', string $sort = 'newest'): Collection
    {
        $results = $this->products
            ->when($category, fn (Collection $c) => $c->filter(fn (Product $p) => $p->category->is($category)))
            ->when($term !== '', fn (Collection $c) => $c->filter(fn (Product $p) => $p->matches($term)));

        $sorted = match ($sort) {
            'price_asc' => $results->sortBy([['price', 'asc'], ['position', 'asc']]),
            'price_desc' => $results->sortBy([['price', 'desc'], ['position', 'asc']]),
            'name' => $results->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE),
            default => $results->sortBy('position'),
        };

        return $sorted->values();
    }
}
