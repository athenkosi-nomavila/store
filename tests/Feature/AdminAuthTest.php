<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private function signUp(array $overrides = []): TestResponse
    {
        return $this->post('/admin/register', $overrides + [
            'name' => 'Thandi',
            'email' => 'thandi@example.com',
            'password' => 'correct-horse',
            'password_confirmation' => 'correct-horse',
        ]);
    }

    public function test_guests_are_sent_to_the_login_page_from_every_admin_page(): void
    {
        foreach (['/admin', '/admin/products', '/admin/products/create', '/admin/users'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
    }

    public function test_the_first_sign_up_becomes_admin_and_is_logged_in(): void
    {
        $this->signUp()->assertRedirect('/admin');

        $user = User::firstWhere('email', 'thandi@example.com');
        $this->assertTrue($user->isAdmin());
        $this->assertAuthenticatedAs($user);
    }

    public function test_later_sign_ups_wait_for_approval(): void
    {
        User::factory()->admin()->create();

        $this->signUp()->assertRedirect('/admin/login')->assertSessionHas('status');

        $this->assertGuest();
        $this->assertFalse(User::firstWhere('email', 'thandi@example.com')->isAdmin());
    }

    public function test_sign_up_cannot_make_itself_admin(): void
    {
        User::factory()->admin()->create();

        $this->signUp(['is_admin' => 1]);

        $this->assertFalse(User::firstWhere('email', 'thandi@example.com')->isAdmin());
    }

    public function test_sign_up_validates_its_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->signUp(['email' => 'taken@example.com'])->assertSessionHasErrors('email');
        $this->signUp(['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $this->signUp(['password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
        $this->signUp(['password_confirmation' => 'different'])->assertSessionHasErrors('password');
        $this->signUp(['name' => ''])->assertSessionHasErrors('name');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_passwords_are_stored_hashed(): void
    {
        $this->signUp();

        $hash = User::firstWhere('email', 'thandi@example.com')->password;
        $this->assertNotSame('correct-horse', $hash);
        $this->assertTrue(Hash::check('correct-horse', $hash));
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

    public function test_pending_users_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'correct-horse']);

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'correct-horse'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_pending_users_with_a_session_are_turned_away(): void
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

    public function test_admin_can_approve_a_pending_user(): void
    {
        $pending = User::factory()->create(['name' => 'Sipho']);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Sipho')
            ->assertSee('Waiting for approval');

        $this->patch("/admin/users/{$pending->id}/approve")->assertRedirect();

        $this->assertTrue($pending->fresh()->isAdmin());
    }

    public function test_admin_can_remove_a_user_but_not_themselves(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->create();

        $this->actingAs($admin)->delete("/admin/users/{$other->id}")->assertRedirect();
        $this->assertModelMissing($other);

        $this->delete("/admin/users/{$admin->id}")->assertSessionHasErrors('user');
        $this->assertModelExists($admin);
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

    public function test_pending_users_cannot_approve_anyone(): void
    {
        $pending = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($pending)->patch("/admin/users/{$other->id}/approve")->assertRedirect('/admin/login');

        $this->assertFalse($other->fresh()->isAdmin());
    }

    public function test_admin_forms_need_a_csrf_token(): void
    {
        // Laravel skips the CSRF check in tests, so call the real middleware.
        $this->app['env'] = 'production';
        $this->withMiddleware();

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'x'])->assertStatus(419);
    }
}
