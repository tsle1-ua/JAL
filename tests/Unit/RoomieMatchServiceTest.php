<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use App\Models\RoomMatch;
use App\Services\RoomieMatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomieMatchServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoomieMatchService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoomieMatchService();
    }

    private function createUserWithProfile(): User
    {
        $user = User::factory()->create();
        Profile::factory()->for($user)->create();
        return $user;
    }

    public function test_like_user_creates_match(): void
    {
        $user1 = $this->createUserWithProfile();
        $user2 = $this->createUserWithProfile();

        $result = $this->service->likeUser($user1->id, $user2->id);

        $this->assertTrue($result['success']);
        $this->assertFalse($result['is_mutual_match']);
        $this->assertDatabaseHas('matches', [
            'id' => $result['match_id'],
        ]);
    }

    public function test_dislike_user_creates_dislike(): void
    {
        $user1 = $this->createUserWithProfile();
        $user2 = $this->createUserWithProfile();

        $result = $this->service->dislikeUser($user1->id, $user2->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('matches', [
            'id' => $result['match_id'],
        ]);

        $match = RoomMatch::find($result['match_id']);
        $this->assertTrue(
            ($match->user_id_1 === $user1->id && $match->user_1_status === 'disliked') ||
            ($match->user_id_2 === $user1->id && $match->user_2_status === 'disliked')
        );
    }

    public function test_mutual_like_sets_matched_at(): void
    {
        $user1 = $this->createUserWithProfile();
        $user2 = $this->createUserWithProfile();

        $this->service->likeUser($user1->id, $user2->id);
        $result = $this->service->likeUser($user2->id, $user1->id);

        $this->assertTrue($result['is_mutual_match']);
        $match = RoomMatch::find($result['match_id']);
        $this->assertNotNull($match->matched_at);
    }
}
