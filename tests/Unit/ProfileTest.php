<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_looking_for_roommate_scope_returns_only_profiles_flagged(): void
    {
        $user1 = User::create([
            'name' => 'One',
            'email' => 'one@example.com',
            'password' => 'password',
        ]);
        $user2 = User::create([
            'name' => 'Two',
            'email' => 'two@example.com',
            'password' => 'password',
        ]);

        Profile::create([
            'user_id' => $user1->id,
            'looking_for_roommate' => true,
        ]);
        Profile::create([
            'user_id' => $user2->id,
            'looking_for_roommate' => false,
        ]);

        $profiles = Profile::lookingForRoommate()->get();

        $this->assertCount(1, $profiles);
        $this->assertTrue($profiles->first()->looking_for_roommate);
    }

    public function test_by_age_range_scope_filters_profiles(): void
    {
        $user1 = User::create(['name' => 'A', 'email' => 'a@example.com', 'password' => 'password']);
        $user2 = User::create(['name' => 'B', 'email' => 'b@example.com', 'password' => 'password']);

        $profile1 = Profile::create(['user_id' => $user1->id, 'age' => 20]);
        $profile2 = Profile::create(['user_id' => $user2->id, 'age' => 30]);

        $results = Profile::byAgeRange(18, 25)->get();

        $this->assertTrue($results->contains($profile1));
        $this->assertFalse($results->contains($profile2));
    }
}
