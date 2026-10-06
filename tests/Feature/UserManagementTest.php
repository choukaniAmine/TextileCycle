<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_back_office(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->role(UserRole::Admin)->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Atelier Test', 'email' => 'a@test.dev', 'role' => 'atelier',
            'password' => 'password123', 'password_confirmation' => 'password123', 'is_active' => 1,
        ])->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', ['email' => 'a@test.dev', 'role' => 'atelier']);
    }

    public function test_public_registration_cannot_create_admin(): void
    {
        $this->post('/inscription', [
            'name' => 'X', 'email' => 'x@test.dev', 'role' => 'admin',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('role');
    }
}
