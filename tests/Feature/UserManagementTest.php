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

    public function test_user_can_register_and_receives_spatie_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
            'role' => 'particulier',
            'city' => 'Paris',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);

        $user = User::where('email', 'jean@example.com')->first();
        $this->assertTrue($user->hasRole('particulier'));
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create(['email' => 'reset@test.dev']);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@test.dev',
        ]);

        $response->assertSessionHas('status');
    }

    public function test_user_can_update_profile_and_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();

        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.png', 100, 'image/png');

        $response = $this->actingAs($user)->put('/profil', [
            'name' => 'Nom Modifie',
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertEquals('Nom Modifie', $user->name);
        $this->assertNotNull($user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_delete_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create(['avatar' => 'avatars/test.jpg']);
        \Illuminate\Support\Facades\Storage::disk('public')->put('avatars/test.jpg', 'fake-image');

        $response = $this->actingAs($user)->delete('/profil/avatar');

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertNull($user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing('avatars/test.jpg');
    }

    public function test_user_can_delete_account_with_current_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->actingAs($user)->delete('/profil', [
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertGuest();
    }

    public function test_user_can_enable_and_disable_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        // Activer 2FA (avec mot de passe confirmé en session)
        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post('/user/two-factor-authentication');

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);

        // Désactiver 2FA
        $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->delete('/user/two-factor-authentication');

        $user->refresh();
        $this->assertNull($user->two_factor_secret);
    }
}
