<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Fill the database with a small, known catalogue. Products are given
     * in newest-first order, like config/catalog.php.
     *
     * @param  array<int, array<string, mixed>>  $products
     */
    private function useCatalog(array $products): void
    {
        $categories = [
            'audio' => Category::factory()->create(['slug' => 'audio', 'name' => 'Audio', 'description' => 'Things that make sound.']),
            'kitchen' => Category::factory()->create(['slug' => 'kitchen', 'name' => 'Kitchen', 'description' => 'Things for cooking.']),
        ];

        foreach (array_values($products) as $i => $p) {
            $p += ['category' => 'audio', 'price' => 100, 'quantity' => 10, 'description' => '', 'active' => true];

            Product::factory()->for($categories[$p['category']])->create([
                'slug' => $p['slug'],
                'sku' => 'SKU-'.$p['slug'],
                'name' => $p['name'],
                'price' => $p['price'],
                'quantity' => $p['quantity'],
                'description' => $p['description'],
                'is_active' => $p['active'],
                'created_at' => now()->subMinutes($i),
            ]);
        }
    }

    private function product(string $name, array $extra = []): array
    {
        return ['slug' => str($name)->slug()->toString(), 'name' => $name] + $extra;
    }

    public function test_homepage_lists_the_eight_newest_active_products(): void
    {
        $products = array_map(fn ($i) => $this->product("Item {$i}"), range(1, 9));
        $products[] = $this->product('Hidden Thing', ['active' => false]);
        $this->useCatalog($products);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Item 1')
            ->assertSee('Item 8')
            ->assertDontSee('Item 9')
            ->assertDontSee('Hidden Thing');
        $this->assertCount(8, $response->viewData('products'));
    }

    public function test_homepage_shows_empty_state_without_products(): void
    {
        $this->useCatalog([]);

        $this->get('/')->assertOk()->assertSee('New stock is on its way');
    }

    public function test_sample_catalogue_renders(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->get('/')->assertOk()->assertSee('Aria Wireless Headphones');
        $this->get('/products/aria-wireless-headphones')->assertOk()->assertSee('STR-0001');
    }

    public function test_product_page_shows_details_and_related_products(): void
    {
        $this->useCatalog([
            $this->product('Speaker', ['quantity' => 3]),
            $this->product('Headphones'),
            $this->product('Kettle', ['category' => 'kitchen']),
        ]);

        $this->get(route('products.show', 'speaker'))
            ->assertOk()
            ->assertSee('Speaker')
            ->assertSee('Only 3 left')
            ->assertSee('Headphones')
            ->assertDontSee('Kettle');
    }

    public function test_inactive_and_missing_products_return_404(): void
    {
        $this->useCatalog([$this->product('Old Thing', ['active' => false])]);

        $this->get('/products/old-thing')->assertNotFound();
        $this->get('/products/does-not-exist')->assertNotFound()->assertSee('find that page');
        $this->get('/category/does-not-exist')->assertNotFound();
    }

    public function test_out_of_stock_product_is_labelled(): void
    {
        $this->useCatalog([$this->product('Sold Out', ['quantity' => 0])]);

        $this->get('/products/sold-out')->assertOk()->assertSee('Out of stock');
    }

    public function test_search_matches_name_and_description(): void
    {
        $this->useCatalog([
            $this->product('Aria Headphones'),
            $this->product('Desk Lamp', ['description' => 'Pairs well with headphones.']),
            $this->product('Coffee Mug', ['description' => 'Stoneware.']),
        ]);

        $this->get('/search?q=HEADPHONES')
            ->assertOk()
            ->assertSee('Aria Headphones')
            ->assertSee('Desk Lamp')
            ->assertDontSee('Coffee Mug')
            ->assertSee('2 products found');
    }

    public function test_search_treats_sql_as_plain_text(): void
    {
        $this->useCatalog([$this->product('Speaker')]);

        $this->get('/search?q='.urlencode("' OR 1=1 --"))
            ->assertOk()
            ->assertDontSee('Speaker')
            ->assertSee('0 products found');
    }

    public function test_search_treats_wildcards_as_plain_text(): void
    {
        $this->useCatalog([
            $this->product('Cotton Tee', ['description' => '100% cotton.']),
            $this->product('Wool Socks', ['description' => '100 pairs sold.']),
            $this->product('Desk_Lamp'),
        ]);

        $this->get('/search?q='.urlencode('100%'))
            ->assertOk()
            ->assertSee('Cotton Tee')
            ->assertDontSee('Wool Socks')
            ->assertSee('1 product found');

        $this->get('/search?q=_')
            ->assertOk()
            ->assertSee('Desk_Lamp')
            ->assertDontSee('Cotton Tee')
            ->assertDontSee('Wool Socks');

        $this->get('/search?q=!')->assertOk()->assertSee('0 products found');
    }

    public function test_database_changes_show_on_the_storefront(): void
    {
        $this->useCatalog([$this->product('Speaker')]);

        Product::where('slug', 'speaker')->update(['name' => 'Loud Speaker', 'price' => 349.5, 'quantity' => 0]);

        $this->get('/products/speaker')
            ->assertOk()
            ->assertSee('Loud Speaker')
            ->assertSee('R 349.50')
            ->assertSee('Out of stock');

        Product::where('slug', 'speaker')->update(['is_active' => false]);

        $this->get('/products/speaker')->assertNotFound();
        $this->get('/')->assertDontSee('Loud Speaker');
    }

    public function test_search_input_is_escaped_in_output(): void
    {
        $payload = '<script>alert("x")</script>';

        $this->get('/search?q='.urlencode($payload))
            ->assertOk()
            ->assertDontSee($payload, false)
            ->assertSee(e($payload), false);
    }

    public function test_search_rejects_invalid_parameters(): void
    {
        $this->get('/search?q='.str_repeat('a', 101))->assertSessionHasErrors('q');
        $this->get('/shop?sort=drop_table')->assertSessionHasErrors('sort');
        $this->get('/shop?category=nope')->assertSessionHasErrors('category');
        $this->get('/search?q[]=array')->assertSessionHasErrors('q');
    }

    public function test_category_page_lists_only_that_category(): void
    {
        $this->useCatalog([
            $this->product('Speaker'),
            $this->product('Kettle', ['category' => 'kitchen']),
        ]);

        $this->get('/category/kitchen')
            ->assertOk()
            ->assertSee('Kettle')
            ->assertDontSee('Speaker');
    }

    public function test_shop_sorts_by_price(): void
    {
        $this->useCatalog([
            $this->product('Pricey Item', ['price' => 900]),
            $this->product('Cheap Item', ['price' => 100]),
        ]);

        $this->get('/shop?sort=price_asc')->assertOk()->assertSeeInOrder(['Cheap Item', 'Pricey Item']);
        $this->get('/shop?sort=price_desc')->assertOk()->assertSeeInOrder(['Pricey Item', 'Cheap Item']);
    }

    public function test_shop_paginates_twelve_per_page(): void
    {
        $this->useCatalog(array_map(fn ($i) => $this->product(sprintf('Item %02d', $i)), range(1, 13)));

        $this->get('/shop')->assertOk()->assertSee('Item 12')->assertDontSee('Item 13');
        $this->get('/shop?page=2')->assertOk()->assertSee('Item 13')->assertDontSee('Item 12');
    }

    public function test_product_text_is_escaped(): void
    {
        $this->useCatalog([$this->product('Lamp', [
            'name' => 'Lamp <img src=x onerror=alert(1)>',
            'description' => '<b>bold</b>',
        ])]);

        $this->get('/products/lamp')
            ->assertOk()
            ->assertDontSee('<img src=x onerror=alert(1)>', false)
            ->assertDontSee('<b>bold</b>', false);
    }

    public function test_responses_carry_security_headers(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');
    }
}
