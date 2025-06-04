<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Carbon\Carbon;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\EloquentEventRepository;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_event_can_be_created_with_image(): void
    {
        Storage::fake('public');
        $user = $this->createUser();

        $data = [
            'title' => 'My Event',
            'description' => 'Event description',
            'date' => Carbon::now()->addDay()->toDateString(),
            'category' => 'social',
            'image' => UploadedFile::fake()->image('event.jpg'),
        ];

        $response = $this->actingAs($user)->post('/events', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'My Event']);
        Storage::disk('public')->assertExists(Event::first()->image_path);
    }

    public function test_event_can_be_updated(): void
    {
        Storage::fake('public');
        $user = $this->createUser();

        $event = Event::create([
            'title' => 'Old',
            'description' => 'Desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $user->id,
            'image_path' => UploadedFile::fake()->image('old.jpg')->store('events', 'public'),
        ]);

        $data = [
            'title' => 'New Title',
            'description' => 'Updated',
            'date' => Carbon::now()->addDays(2)->toDateString(),
            'category' => 'social',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ];

        $response = $this->actingAs($user)->patch("/events/{$event->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'New Title']);
        Storage::disk('public')->assertExists($event->fresh()->image_path);
    }

    public function test_event_visibility_can_be_toggled(): void
    {
        $user = $this->createUser();

        $event = Event::create([
            'title' => 'Visible',
            'description' => 'desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $user->id,
            'is_public' => true,
        ]);

        $this->actingAs($user);
        $service = app(\App\Services\EventService::class);
        $this->assertTrue($service->toggleEventVisibility($event->id));
        $this->assertFalse($event->fresh()->is_public);
    }

    public function test_user_can_register_for_event(): void
    {
        $owner = $this->createUser();
        $attendee = User::create([
            'name' => 'Other',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $event = Event::create([
            'title' => 'Joinable',
            'description' => 'desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $owner->id,
            'max_attendees' => 5,
        ]);

        $this->actingAs($attendee);
        $service = app(\App\Services\EventService::class);
        $this->assertTrue($service->registerAttendance($event->id));
        $this->assertDatabaseHas('event_user', [
            'event_id' => $event->id,
            'user_id' => $attendee->id,
        ]);
        $this->assertEquals(1, $event->fresh()->current_attendees);
    }
}
