<?php

namespace Tests\Feature;

use App\Models\RoomMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BroadcastAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorizes_when_user_is_match_member(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = RoomMatch::create([
            'user_id_1' => $user1->id,
            'user_id_2' => $user2->id,
            'user_1_status' => 'liked',
            'user_2_status' => 'liked',
            'matched_at' => now(),
        ]);

        $response = $this->actingAs($user1)->post('/broadcasting/auth', [
            'socket_id' => '1234.5678',
            'channel_name' => "private-match.{$match->id}",
        ]);

        $response->assertStatus(200);
    }

    public function test_denies_when_user_is_not_match_member(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $outsider = User::factory()->create();

        $match = RoomMatch::create([
            'user_id_1' => $user1->id,
            'user_id_2' => $user2->id,
            'user_1_status' => 'liked',
            'user_2_status' => 'liked',
            'matched_at' => now(),
        ]);

        $response = $this->actingAs($outsider)->post('/broadcasting/auth', [
            'socket_id' => '1234.5678',
            'channel_name' => "private-match.{$match->id}",
        ]);

        $response->assertStatus(403);
    }
}
