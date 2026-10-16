<?php

namespace Tests\Feature;

use App\Catalog\Catalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Route;
use PDOException;
use RuntimeException;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Visitors never see debug pages, so test what production shows.
        config(['app.debug' => false]);
        Exceptions::fake();
    }

    /** Make every catalogue query fail the way it does when MySQL is down. */
    private function breakTheDatabase(): void
    {
        $outage = new QueryException('mysql', 'select * from `products`', [],
            new PDOException('SQLSTATE[HY000] [2002] Connection refused'));

        $this->mock(Catalog::class, function ($mock) use ($outage) {
            $mock->shouldReceive('categories', 'latest', 'product')->andThrow($outage);
        });
    }

    public function test_database_outage_shows_the_back_soon_page(): void
    {
        $this->breakTheDatabase();

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', '60')
            ->assertSee("We'll be back shortly")
            ->assertDontSee('Connection refused');

        Exceptions::assertReported(QueryException::class);
    }

    public function test_database_outage_shows_the_back_soon_page_in_debug_mode_too(): void
    {
        config(['app.debug' => true]);
        $this->breakTheDatabase();

        $this->get('/')
            ->assertStatus(503)
            ->assertSee("We'll be back shortly")
            ->assertHeader('Content-Security-Policy');
    }

    public function test_debug_error_page_is_not_blocked_by_the_content_security_policy(): void
    {
        config(['app.debug' => true]);
        $this->mock(Catalog::class, function ($mock) {
            $mock->shouldReceive('categories')->andReturn(new Collection);
            $mock->shouldReceive('latest')->andThrow(new RuntimeException('a bug'));
        });

        $this->get('/')
            ->assertStatus(500)
            ->assertHeaderMissing('Content-Security-Policy')
            ->assertHeader('X-Frame-Options', 'DENY');

        // Visitors' error pages keep the policy.
        config(['app.debug' => false]);
        $this->get('/')->assertStatus(500)->assertHeader('Content-Security-Policy');
    }

    public function test_database_outage_answers_json_clients_with_json(): void
    {
        $this->breakTheDatabase();

        $this->getJson('/products/anything')
            ->assertStatus(503)
            ->assertExactJson(['message' => 'Service temporarily unavailable.']);
    }

    public function test_a_broken_query_is_a_server_error_not_an_outage(): void
    {
        $bug = new QueryException('mysql', 'select * from `prodcts`', [],
            new PDOException("SQLSTATE[42S02]: Base table or view not found: 1146 Table 'store.prodcts' doesn't exist"));
        $this->mock(Catalog::class, fn ($mock) => $mock->shouldReceive('categories', 'latest')->andThrow($bug));

        $this->get('/')
            ->assertStatus(500)
            ->assertSee('Something went wrong on our side')
            ->assertDontSee('prodcts');
    }

    public function test_unexpected_errors_show_the_server_error_page_without_details(): void
    {
        $this->mock(Catalog::class, function ($mock) {
            $mock->shouldReceive('categories')->andReturn(new Collection);
            $mock->shouldReceive('latest')->andThrow(new RuntimeException('secret internals'));
        });

        $this->get('/')
            ->assertStatus(500)
            ->assertSee('Something went wrong on our side')
            ->assertDontSee('secret internals');

        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_missing_page_shows_the_not_found_page(): void
    {
        $this->get('/no-such-page')
            ->assertNotFound()
            ->assertSee("We couldn't find that page");
    }

    public function test_other_client_errors_use_the_fallback_page(): void
    {
        $this->post('/shop')
            ->assertStatus(405)
            ->assertSee("We couldn't complete that request");
    }

    public function test_every_status_laravel_has_a_default_page_for_uses_the_store_layout(): void
    {
        Route::get('/_status/{code}', fn (int $code) => abort($code))->middleware('web');

        foreach ([403, 404, 419, 429, 500, 503] as $code) {
            $this->get("/_status/{$code}")
                ->assertStatus($code)
                ->assertSee('Skip to content');
        }
    }

    public function test_maintenance_mode_shows_the_back_soon_page(): void
    {
        $this->artisan('down')->assertSuccessful();

        try {
            $this->get('/')->assertStatus(503)->assertSee("We'll be back shortly");
        } finally {
            $this->artisan('up');
        }
    }
}
