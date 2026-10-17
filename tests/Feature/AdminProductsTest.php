<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('product_images');
        $this->category = Category::factory()->create(['name' => 'Audio', 'slug' => 'audio']);
        $this->actingAs(User::factory()->admin()->create());
    }

    /** @return array<string, mixed> */
    private function form(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Nova Speaker',
            'category_id' => $this->category->id,
            'price' => '1299.50',
            'quantity' => '7',
            'description' => 'Small and loud.',
            'is_active' => '1',
        ];
    }

    public function test_admin_can_add_a_product_and_it_shows_on_the_store(): void
    {
        $this->post('/admin/products', $this->form())
            ->assertRedirect('/admin/products')
            ->assertSessionHas('status');

        $product = Product::firstWhere('name', 'Nova Speaker');
        $this->assertSame('nova-speaker', $product->slug);
        $this->assertSame('1299.50', $product->price);
        $this->assertSame(7, $product->quantity);
        $this->assertTrue($product->is_active);

        $this->get('/products/nova-speaker')->assertOk()->assertSee('Nova Speaker')->assertSee('R 1,299.50');
        $this->get('/')->assertSee('Nova Speaker');
    }

    public function test_slugs_stay_unique(): void
    {
        Product::factory()->create(['slug' => 'nova-speaker']);

        $this->post('/admin/products', $this->form());

        $this->assertSame('nova-speaker-2', Product::firstWhere('name', 'Nova Speaker')->slug);
    }

    public function test_the_sku_is_the_next_free_number(): void
    {
        $this->post('/admin/products', $this->form());
        $this->assertSame('STR-0001', Product::sole()->sku);

        Product::factory()->create(['sku' => 'STR-0012']);
        Product::factory()->create(['sku' => 'CUSTOM-99']);

        $this->post('/admin/products', $this->form(['name' => 'Second', 'sku' => 'STR-0999']))->assertSessionHasNoErrors();

        $this->assertSame('STR-0013', Product::firstWhere('name', 'Second')->sku, 'A typed-in SKU is ignored');
    }

    public function test_the_sku_cannot_be_changed_when_editing(): void
    {
        $product = Product::factory()->for($this->category)->create(['sku' => 'STR-0100']);

        $this->put("/admin/products/{$product->id}", $this->form(['sku' => 'STR-0555']))->assertSessionHasNoErrors();

        $this->assertSame('STR-0100', $product->fresh()->sku);
    }

    public function test_a_product_can_be_added_hidden(): void
    {
        $this->post('/admin/products', $this->form(['is_active' => '0']));

        $this->assertFalse(Product::firstWhere('name', 'Nova Speaker')->is_active);
        $this->get('/products/nova-speaker')->assertNotFound();
    }

    public function test_the_form_is_validated(): void
    {
        $cases = [
            'name' => ['name' => ''],
            'category_id' => ['category_id' => 999],
            'price' => ['price' => '-1'],
            'quantity' => ['quantity' => '1.5'],
        ];

        foreach ($cases as $field => $bad) {
            $this->post('/admin/products', $this->form($bad))->assertSessionHasErrors($field);
        }
        $this->post('/admin/products', $this->form(['price' => '9.999']))->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_extra_fields_are_ignored(): void
    {
        $this->post('/admin/products', $this->form(['slug' => 'hijacked', 'image' => null, 'id' => 999]));

        $product = Product::firstWhere('name', 'Nova Speaker');
        $this->assertSame('nova-speaker', $product->slug);
        $this->assertNotSame(999, $product->id);
    }

    public function test_an_uploaded_image_is_stored_under_a_random_name(): void
    {
        $this->post('/admin/products', $this->form([
            'image' => UploadedFile::fake()->image('../../evil.php.png', 400, 400),
        ]))->assertSessionHasNoErrors();

        $image = Product::firstWhere('name', 'Nova Speaker')->image;
        $this->assertMatchesRegularExpression('/^upload-[A-Za-z0-9]{40}\.png$/', $image);
        Storage::disk('product_images')->assertExists($image);
        $this->get('/products/nova-speaker')->assertSee('/images/products/'.$image, false);
    }

    public function test_unsafe_or_oversized_files_are_refused(): void
    {
        $files = [
            'svg' => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            'php disguised as jpg' => UploadedFile::fake()->createWithContent('photo.jpg', '<?php echo "hi"; ?>'),
            'pdf' => UploadedFile::fake()->create('menu.pdf', 100, 'application/pdf'),
            'too big' => UploadedFile::fake()->image('big.jpg', 800, 800)->size(3000),
            'too small' => UploadedFile::fake()->image('tiny.png', 10, 10),
        ];

        foreach ($files as $case => $file) {
            $this->post('/admin/products', $this->form(['image' => $file]))
                ->assertSessionHasErrors('image', "Expected the {$case} upload to be refused");
        }

        $this->assertDatabaseCount('products', 0);
        $this->assertEmpty(Storage::disk('product_images')->allFiles());
    }

    public function test_admin_can_edit_a_product(): void
    {
        $product = Product::factory()->for($this->category)->create(['slug' => 'old-name', 'sku' => 'STR-0100']);

        $this->put("/admin/products/{$product->id}", $this->form(['name' => 'New Name', 'quantity' => '0']))
            ->assertRedirect('/admin/products');

        $product->refresh();
        $this->assertSame('New Name', $product->name);
        $this->assertSame('old-name', $product->slug, 'The web address stays the same');
        $this->get('/products/old-name')->assertSee('New Name')->assertSee('Out of stock');
    }

    public function test_replacing_an_image_deletes_the_old_upload_but_never_a_seeded_picture(): void
    {
        $disk = Storage::disk('product_images');
        $disk->put('headphones.jpg', 'seeded');
        $product = Product::factory()->for($this->category)->create(['image' => 'headphones.jpg', 'sku' => 'STR-0100']);

        $this->put("/admin/products/{$product->id}", $this->form(['image' => UploadedFile::fake()->image('a.jpg', 300, 300)]));
        $first = $product->fresh()->image;
        $disk->assertExists(['headphones.jpg', $first]);

        $this->put("/admin/products/{$product->id}", $this->form(['image' => UploadedFile::fake()->image('b.jpg', 300, 300)]));
        $disk->assertMissing($first);
        $disk->assertExists($product->fresh()->image);
    }

    public function test_editing_without_a_new_image_keeps_the_old_one(): void
    {
        $product = Product::factory()->for($this->category)->create(['image' => 'headphones.jpg', 'sku' => 'STR-0100']);

        $this->put("/admin/products/{$product->id}", $this->form());

        $this->assertSame('headphones.jpg', $product->fresh()->image);
    }

    public function test_delete_asks_first_then_removes_the_product_and_its_upload(): void
    {
        $this->post('/admin/products', $this->form(['image' => UploadedFile::fake()->image('a.png', 300, 300)]));
        $product = Product::firstWhere('name', 'Nova Speaker');

        $this->get("/admin/products/{$product->id}/delete")->assertOk()->assertSee('Yes, delete it');
        $this->assertModelExists($product);

        $this->delete("/admin/products/{$product->id}")->assertRedirect('/admin/products');

        $this->assertModelMissing($product);
        Storage::disk('product_images')->assertMissing($product->image);
        $this->get('/products/nova-speaker')->assertNotFound();
    }

    public function test_guests_and_pending_users_cannot_change_products(): void
    {
        $product = Product::factory()->create();

        foreach ([null, User::factory()->create()] as $user) {
            auth()->logout();
            if ($user) {
                $this->actingAs($user);
            }

            $this->post('/admin/products', $this->form())->assertRedirect('/admin/login');
            $this->put("/admin/products/{$product->id}", $this->form())->assertRedirect('/admin/login');
            $this->delete("/admin/products/{$product->id}")->assertRedirect('/admin/login');
            $this->patch("/admin/products/{$product->id}/visibility", ['is_active' => '0'])->assertRedirect('/admin/login');
        }

        $this->assertDatabaseCount('products', 1);
        $this->assertModelExists($product);
        $this->assertTrue($product->fresh()->is_active);
    }

    public function test_a_product_can_be_hidden_and_shown_from_the_list(): void
    {
        $product = Product::factory()->for($this->category)->create(['name' => 'Nova Speaker', 'slug' => 'nova-speaker', 'is_active' => true]);

        $this->get('/admin/products')->assertSee('Hide Nova Speaker');

        $this->from('/admin/products')->patch("/admin/products/{$product->id}/visibility", ['is_active' => '0'])
            ->assertRedirect('/admin/products')
            ->assertSessionHas('status', 'Nova Speaker is now hidden from the store.');
        $this->assertFalse($product->fresh()->is_active);
        $this->get('/products/nova-speaker')->assertNotFound();
        $this->get('/admin/products')->assertSee('Show Nova Speaker');

        $this->from('/admin/products')->patch("/admin/products/{$product->id}/visibility", ['is_active' => '1'])
            ->assertSessionHas('status', 'Nova Speaker is now showing on the store.');
        $this->assertTrue($product->fresh()->is_active);
        $this->get('/products/nova-speaker')->assertOk();
    }

    public function test_the_visibility_change_is_validated(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->patch("/admin/products/{$product->id}/visibility", [])->assertSessionHasErrors('is_active');
        $this->patch("/admin/products/{$product->id}/visibility", ['is_active' => 'maybe'])->assertSessionHasErrors('is_active');
        $this->assertTrue($product->fresh()->is_active);
    }
}
