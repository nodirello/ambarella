<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_homepage(): void
    {
        $this->get('/')->assertOk()->assertSee('AMBARELLA');
    }

    public function test_register_creates_user_and_redirects_to_onboarding(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jasur Toshmatov',
            'email' => 'jasur@example.uz',
            'phone' => '+998901234567',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertDatabaseHas('users', ['email' => 'jasur@example.uz']);
    }

    public function test_registration_rejects_weak_password(): void
    {
        $this->post('/register', [
            'name' => 'Jasur',
            'email' => 'jasur@example.uz',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_login_and_logout_flow(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_banned_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['is_banned' => true]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_only_area_rejects_regular_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
