<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Loads the sample catalogue from config/catalog.php into the database.
 *
 * Safe to run more than once: rows are matched on their slug and updated.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [];

        foreach (config('catalog.categories') as $slug => $category) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $category['name'], 'description' => $category['description'] ?? null],
            );
        }

        // The config lists products newest first, so each later entry is
        // given an earlier created_at to keep that order.
        foreach (config('catalog.products') as $i => $product) {
            $model = Product::updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'description' => $product['description'] ?? null,
                    'price' => $product['price'],
                    'quantity' => $product['quantity'],
                    'image' => $product['image'] ?? null,
                    'is_active' => $product['active'] ?? true,
                ],
            );
            $model->created_at = now()->subMinutes($i);
            $model->save();
        }
    }
}
