<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_toggle_user_admin_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-admin");
        $response->assertStatus(302);
        $this->assertTrue($user->fresh()->is_admin);

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->id}/toggle-admin");
        $response->assertStatus(302);
        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_non_admin_cannot_toggle_user_admin_status(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $target = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->patch("/admin/users/{$target->id}/toggle-admin");
        $response->assertRedirect('/');
        $this->assertFalse($target->fresh()->is_admin);
    }

    public function test_guest_is_redirected_to_login_when_toggling(): void
    {
        $user = User::factory()->create();

        $response = $this->patch("/admin/users/{$user->id}/toggle-admin");
        $response->assertRedirect('/login');
        $this->assertFalse($user->fresh()->is_admin);
    }
}
