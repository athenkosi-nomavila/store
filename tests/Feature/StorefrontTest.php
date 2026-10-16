<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_latest_active_products(): void
    {
        $products = Product::factory()->count(9)->create();
        $hidden = Product::factory()->inactive()->create(['name' => 'Hidden Thing']);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee($products->last()->name)
            ->assertDontSee($hidden->name);
        $this->assertCount(8, $response->viewData('products'));
    }

    public function test_homepage_shows_empty_state_without_products(): void
    {
        $this->get('/')->assertOk()->assertSee('New stock is on its way');
    }

    public function test_product_page_shows_details_and_related_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create(['quantity' => 3]);
        $related = Product::factory()->for($category)->create();
        $other = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Only 3 left')
            ->assertSee($related->name)
            ->assertDontSee($other->name);
    }

    public function test_inactive_and_missing_products_return_404(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product))->assertNotFound();
        $this->get('/products/does-not-exist')->assertNotFound()->assertSee('find that page');
    }

    public function test_out_of_stock_product_disables_buying(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Out of stock')
            ->assertSee('disabled', false);
    }

    public function test_search_matches_name_and_description(): void
    {
        Product::factory()->create(['name' => 'Aria Headphones']);
        Product::factory()->create(['name' => 'Desk Lamp', 'description' => 'Pairs well with headphones.']);
        Product::factory()->create(['name' => 'Coffee Mug', 'description' => 'Stoneware.']);

        $this->get('/search?q=headphones')
            ->assertOk()
            ->assertSee('Aria Headphones')
            ->assertSee('Desk Lamp')
            ->assertDontSee('Coffee Mug')
            ->assertSee('2 products found');
    }

    public function test_search_treats_like_wildcards_literally(): void
    {
        Product::factory()->create(['name' => 'Plain Product', 'description' => 'Nothing special.']);

        $this->get('/search?q=%25')->assertOk()->assertSee('No products match');
        $this->get('/search?q=_')->assertOk()->assertSee('No products match');
    }

    public function test_search_input_is_escaped_in_output(): void
    {
        $payload = '<script>alert("x")</script>';

        $this->get('/search?q='.urlencode($payload))
            ->assertOk()
            ->assertDontSee($payload, false)
            ->assertSee(e($payload), false);
    }

    public function test_sql_injection_attempt_is_treated_as_text(): void
    {
        Product::factory()->count(3)->create();

        $this->get('/search?q='.urlencode("' OR 1=1 --"))
            ->assertOk()
            ->assertSee('0 products found');
        $this->assertSame(3, Product::count());
    }

    public function test_search_rejects_invalid_parameters(): void
    {
        $this->get('/search?q='.str_repeat('a', 101))->assertSessionHasErrors('q');
        $this->get('/shop?sort=drop_table')->assertSessionHasErrors('sort');
        $this->get('/search?q[]=array')->assertSessionHasErrors('q');
    }

    public function test_category_page_lists_only_that_category(): void
    {
        $audio = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);
        $inAudio = Product::factory()->for($audio)->create();
        $elsewhere = Product::factory()->create();

        $this->get('/category/audio')
            ->assertOk()
            ->assertSee($inAudio->name)
            ->assertDontSee($elsewhere->name);
    }

    public function test_shop_sorts_by_price(): void
    {
        Product::factory()->create(['name' => 'Pricey Item', 'price' => 900]);
        Product::factory()->create(['name' => 'Cheap Item', 'price' => 100]);

        $this->get('/shop?sort=price_asc')->assertOk()->assertSeeInOrder(['Cheap Item', 'Pricey Item']);
        $this->get('/shop?sort=price_desc')->assertOk()->assertSeeInOrder(['Pricey Item', 'Cheap Item']);
    }

    public function test_product_text_is_escaped(): void
    {
        $product = Product::factory()->create([
            'name' => 'Lamp <img src=x onerror=alert(1)>',
            'description' => '<b>bold</b>',
        ]);

        $this->get(route('products.show', $product))
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

    public function test_newsletter_rejects_missing_csrf_token(): void
    {
        // Laravel skips the CSRF check while running tests, so turn it back on here.
        $this->app['env'] = 'local';

        $this->post('/newsletter', ['email' => 'a@example.com'])->assertStatus(419);
    }

    public function test_newsletter_validates_email(): void
    {
        $this->post('/newsletter', ['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $this->post('/newsletter', ['email' => 'a@example.com'])->assertSessionHas('status');
    }
}
