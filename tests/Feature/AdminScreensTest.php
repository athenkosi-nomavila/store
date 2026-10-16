<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_renders_without_the_storefront_layout(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Log in to the admin')
            ->assertSee('name="_token"', false)
            ->assertDontSee('site-search', false);
    }

    public function test_login_and_sign_up_screens_link_to_each_other(): void
    {
        $this->get('/admin/login')->assertSee(route('admin.register'));

        $this->get('/admin/register')
            ->assertOk()
            ->assertSee('Create an account')
            ->assertSee('name="password_confirmation"', false)
            ->assertSee(route('admin.login'))
            ->assertDontSee('site-search', false);
    }

    public function test_dashboard_shows_the_four_stat_cards(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSeeInOrder(['Total products', 'Active products', 'Out of stock', 'Categories'])
            ->assertDontSee('site-search', false);
    }

    public function test_product_list_includes_hidden_products(): void
    {
        Product::factory()->create(['name' => 'Visible Lamp']);
        Product::factory()->create(['name' => 'Hidden Kettle', 'is_active' => false]);

        $this->get('/admin/products')
            ->assertOk()
            ->assertSee('Visible Lamp')
            ->assertSee('Hidden Kettle')
            ->assertSee('Hidden');
    }

    public function test_product_list_shows_an_empty_state(): void
    {
        $this->get('/admin/products')->assertOk()->assertSee('No products yet');
    }

    public function test_add_form_lists_categories(): void
    {
        Category::factory()->create(['name' => 'Kitchen']);

        $this->get('/admin/products/create')
            ->assertOk()
            ->assertSee('Kitchen')
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_edit_form_is_filled_in_and_found_by_id(): void
    {
        $product = Product::factory()->create(['name' => 'Orbit Watch', 'sku' => 'STR-0042']);

        $this->get("/admin/products/{$product->id}/edit")
            ->assertOk()
            ->assertSee('value="Orbit Watch"', false)
            ->assertSee('value="STR-0042"', false);

        $this->get('/admin/products/999/edit')->assertNotFound();
        $this->get("/admin/products/{$product->slug}/edit")->assertNotFound();
    }

    public function test_product_text_is_escaped(): void
    {
        Product::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->get('/admin/products')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_admin_pages_are_not_indexed_and_carry_security_headers(): void
    {
        $this->get('/admin')
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');
    }
}
