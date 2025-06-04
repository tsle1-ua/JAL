<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Profile $profile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->profile = Profile::factory()->for($this->user)->create();
    }

    public function test_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Mi Perfil');
    }

    public function test_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->user)->patch('/profile', [
            'bio' => 'Nueva bio',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('profiles', [
            'id' => $this->profile->id,
            'bio' => 'Nueva bio',
        ]);
    }

    public function test_user_can_upload_profile_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)->patch('/profile', [
            'profile_image' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertRedirect(route('profile.show'));
        $profile = $this->profile->fresh();
        Storage::disk('public')->assertExists($profile->profile_image);
    }
}
