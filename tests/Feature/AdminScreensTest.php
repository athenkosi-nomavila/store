<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    /** Admin pages are only for a logged-in admin. */
    private function asAdmin(): static
    {
        return $this->actingAs(User::factory()->admin()->create());
    }

    public function test_login_screen_renders_without_the_storefront_layout(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Admin login')
            ->assertSee('name="_token"', false)
            ->assertDontSee('site-search', false);
    }

    public function test_there_is_no_sign_up_page(): void
    {
        $this->get('/admin/login')->assertDontSee('Create an account');
        $this->get('/admin/register')->assertNotFound();
        $this->post('/admin/register', ['email' => 'a@example.com'])->assertNotFound();
    }

    public function test_dashboard_shows_the_four_stat_cards(): void
    {
        $this->asAdmin()->get('/admin')
            ->assertOk()
            ->assertSeeInOrder(['Total products', 'Active products', 'Out of stock', 'Categories'])
            ->assertDontSee('site-search', false);
    }

    public function test_dashboard_counts_come_from_the_database(): void
    {
        $category = Category::factory()->create();
        Category::factory()->create();
        Product::factory()->count(2)->for($category)->create(['quantity' => 10]);
        Product::factory()->for($category)->create(['quantity' => 0]);
        Product::factory()->for($category)->create(['is_active' => false, 'quantity' => 0]);

        $this->asAdmin()->get('/admin')
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats) => array_column($stats, 1) === [4, 3, 2, 2]);
    }

    public function test_product_list_includes_hidden_products(): void
    {
        Product::factory()->create(['name' => 'Visible Lamp']);
        Product::factory()->create(['name' => 'Hidden Kettle', 'is_active' => false]);

        $this->asAdmin()->get('/admin/products')
            ->assertOk()
            ->assertSee('Visible Lamp')
            ->assertSee('Hidden Kettle')
            ->assertSee('Hidden');
    }

    public function test_product_list_shows_an_empty_state(): void
    {
        $this->asAdmin()->get('/admin/products')->assertOk()->assertSee('No products yet');
    }

    public function test_add_form_lists_categories(): void
    {
        Category::factory()->create(['name' => 'Kitchen']);

        $this->asAdmin()->get('/admin/products/create')
            ->assertOk()
            ->assertSee('Kitchen')
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_edit_form_is_filled_in_and_found_by_id(): void
    {
        $product = Product::factory()->create(['name' => 'Orbit Watch', 'sku' => 'STR-0042']);

        $this->asAdmin()->get("/admin/products/{$product->id}/edit")
            ->assertOk()
            ->assertSee('value="Orbit Watch"', false)
            ->assertSee('value="STR-0042"', false);

        $this->asAdmin()->get('/admin/products/999/edit')->assertNotFound();
        $this->asAdmin()->get("/admin/products/{$product->slug}/edit")->assertNotFound();
    }

    public function test_product_text_is_escaped(): void
    {
        Product::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->asAdmin()->get('/admin/products')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_admin_pages_are_not_indexed_and_carry_security_headers(): void
    {
        $this->asAdmin()->get('/admin')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');
    }
}
