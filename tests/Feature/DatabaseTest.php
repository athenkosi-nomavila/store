<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CatalogSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_product_belongs_to_a_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $this->assertTrue($product->category->is($category));
        $this->assertTrue($category->products->first()->is($product));
    }

    public function test_a_category_with_products_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->expectException(QueryException::class);

        $product->category->delete();
    }

    public function test_an_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $category->delete();

        $this->assertModelMissing($category);
    }

    public function test_slug_and_sku_are_unique(): void
    {
        $product = Product::factory()->create();

        $this->expectException(QueryException::class);

        Product::factory()->create(['slug' => $product->slug]);
    }

    public function test_the_seeder_loads_the_sample_catalogue(): void
    {
        $this->seed(CatalogSeeder::class);
        // Running it again must not create duplicates.
        $this->seed(CatalogSeeder::class);

        $this->assertSame(count(config('catalog.categories')), Category::count());
        $this->assertSame(count(config('catalog.products')), Product::count());

        $watch = Product::where('slug', 'orbit-smartwatch')->firstOrFail();
        $this->assertSame('wearables', $watch->category->slug);
        $this->assertSame('3299.00', $watch->price);
        $this->assertSame(15, $watch->quantity);
        $this->assertTrue($watch->is_active);

        // The first product in the config is the newest.
        $first = config('catalog.products')[0]['slug'];
        $this->assertSame($first, Product::latest()->first()->slug);
    }
}
