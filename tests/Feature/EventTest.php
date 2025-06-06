<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Tag;
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

    public function test_registration_fails_when_event_capacity_reached(): void
    {
        $owner = $this->createUser();
        $firstAttendee = User::create([
            'name' => 'Attendee 1',
            'email' => 'first@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $secondAttendee = User::create([
            'name' => 'Attendee 2',
            'email' => 'second@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $event = Event::create([
            'title' => 'Limited',
            'description' => 'desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $owner->id,
            'max_attendees' => 1,
        ]);

        $service = app(\App\Services\EventService::class);

        $this->actingAs($firstAttendee);
        $this->assertTrue($service->registerAttendance($event->id));
        $this->assertEquals(1, $event->fresh()->current_attendees);

        $this->actingAs($secondAttendee);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No hay cupos disponibles para este evento.');
        $service->registerAttendance($event->id);
        $this->assertEquals(1, $event->fresh()->current_attendees);
    }

    public function test_user_cannot_register_twice_for_same_event(): void
    {
        $owner = $this->createUser();
        $attendee = User::create([
            'name' => 'Repeat',
            'email' => 'repeat@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $event = Event::create([
            'title' => 'Repeatable',
            'description' => 'desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $owner->id,
            'max_attendees' => 2,
        ]);

        $service = app(\App\Services\EventService::class);

        $this->actingAs($attendee);
        $this->assertTrue($service->registerAttendance($event->id));
        $this->assertEquals(1, $event->fresh()->current_attendees);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ya estás registrado en este evento.');
        $service->registerAttendance($event->id);
        $this->assertEquals(1, $event->fresh()->current_attendees);
    }

    public function test_event_can_have_multiple_tags(): void
    {
        $user = $this->createUser();
        $tag1 = Tag::create(['name' => 'Music']);
        $tag2 = Tag::create(['name' => 'Outdoor']);

        $event = Event::create([
            'title' => 'Tagged Event',
            'description' => 'desc',
            'date' => Carbon::now()->addDay(),
            'category' => 'social',
            'user_id' => $user->id,
        ]);

        $event->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $event->tags);
        $this->assertTrue($event->tags->contains($tag1));
        $this->assertTrue($event->tags->contains($tag2));
    }
}
