<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page_from_every_admin_page(): void
    {
        foreach (['/admin', '/admin/products', '/admin/products/create'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
    }

    public function test_an_admin_can_log_in_and_the_session_is_renewed(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-horse']);
        $this->get('/admin/login');
        $before = session()->getId();

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'correct-horse'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($before, session()->getId());
    }

    public function test_wrong_password_gives_a_generic_error(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-horse']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])
            ->assertSessionHasErrors(['email' => __('auth.failed')]);
        $this->post('/admin/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors(['email' => __('auth.failed')]);

        $this->assertGuest();
    }

    public function test_non_admin_accounts_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse']);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'correct-horse'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_non_admin_accounts_with_a_session_are_turned_away(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_login_locks_after_five_failed_attempts(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-horse']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong']);
        }

        // Even the right password is refused during the lockout.
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'correct-horse'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_logout_needs_a_post_request(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/logout')
            ->assertStatus(405);
    }

    public function test_logged_in_admins_skip_the_login_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/login')
            ->assertRedirect('/admin');
    }

    public function test_the_seeder_creates_the_admin_from_env_settings(): void
    {
        config(['admin.name' => 'Thandi', 'admin.email' => 'Thandi@Example.com', 'admin.password' => 'correct-horse']);

        $this->seed(AdminSeeder::class);

        $admin = User::firstWhere('email', 'thandi@example.com');
        $this->assertTrue($admin->isAdmin());
        $this->assertNotSame('correct-horse', $admin->password);
        $this->assertTrue(Hash::check('correct-horse', $admin->password));

        $this->post('/admin/login', ['email' => 'thandi@example.com', 'password' => 'correct-horse'])->assertRedirect('/admin');
    }

    public function test_seeding_again_never_resets_the_admin_password(): void
    {
        config(['admin.email' => 'thandi@example.com', 'admin.password' => 'correct-horse']);
        $this->seed(AdminSeeder::class);
        User::firstWhere('email', 'thandi@example.com')->update(['password' => 'changed-later']);

        config(['admin.password' => 'correct-horse']);
        $this->seed(AdminSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertTrue(Hash::check('changed-later', User::first()->password));
    }

    public function test_the_seeder_skips_missing_or_weak_settings(): void
    {
        config(['admin.email' => null, 'admin.password' => null]);
        $this->seed(AdminSeeder::class);

        config(['admin.email' => 'thandi@example.com', 'admin.password' => 'short']);
        $this->seed(AdminSeeder::class);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_there_is_no_users_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/users')->assertNotFound();
        $this->post('/admin/users', ['name' => 'Eve', 'email' => 'eve@example.com'])->assertNotFound();
        $this->assertDatabaseMissing('users', ['email' => 'eve@example.com']);
    }

    public function test_a_removed_admin_loses_access_straight_away(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'correct-horse']);
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'correct-horse']);
        $this->get('/admin')->assertOk();

        $admin->delete();
        $this->app['auth']->forgetGuards(); // each real request loads the user afresh

        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_forms_need_a_csrf_token(): void
    {
        // Laravel skips the CSRF check in tests, so call the real middleware.
        $this->app['env'] = 'production';
        $this->withMiddleware();

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'x'])->assertStatus(419);
    }
}
