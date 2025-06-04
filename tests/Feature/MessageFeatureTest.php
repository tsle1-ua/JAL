<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserMatch;
use App\Services\RoomieMatchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_message_and_retrieve_conversation(): void
    {
        $service = new RoomieMatchService();
        if (!is_dir(public_path('build'))) {
            mkdir(public_path('build'), 0777, true);
        }
        file_put_contents(
            public_path('build/manifest.json'),
            json_encode([
                'resources/js/app.js' => ['file' => 'app.js', 'src' => 'resources/js/app.js'],
                'resources/sass/app.scss' => ['file' => 'app.css', 'src' => 'resources/sass/app.scss'],
            ])
        );
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Profile::factory()->for($user1)->create();
        Profile::factory()->for($user2)->create();

        $service->likeUser($user1->id, $user2->id);
        $result = $service->likeUser($user2->id, $user1->id);
        $matchId = $result['match_id'];

        $this->actingAs($user1);
        $response = $this->post("/roomie-match/message/{$matchId}", [
            'content' => 'Hola',
        ]);
        $response->assertStatus(302);

        $this->assertDatabaseHas('messages', [
            'match_id' => $matchId,
            'sender_id' => $user1->id,
            'content' => 'Hola',
        ]);

        $response = $this->get("/roomie-match/conversation/{$matchId}");
        $response->assertStatus(200);
        $response->assertSee('Hola');
    }
}
