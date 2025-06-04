<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_scope_returns_only_public_events(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'eventuser@example.com',
            'password' => 'password',
        ]);

        $place = Place::create([
            'user_id' => $user->id,
            'name' => 'Place',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'latitude' => 40.0,
            'longitude' => -3.0,
        ]);

        Event::create([
            'title' => 'Public Event',
            'description' => 'desc',
            'date' => now()->toDateString(),
            'place_id' => $place->id,
            'user_id' => $user->id,
            'category' => 'social',
            'is_public' => true,
        ]);

        Event::create([
            'title' => 'Private Event',
            'description' => 'desc',
            'date' => now()->toDateString(),
            'place_id' => $place->id,
            'user_id' => $user->id,
            'category' => 'social',
            'is_public' => false,
        ]);

        $events = Event::public()->get();

        $this->assertCount(1, $events);
        $this->assertTrue($events->first()->is_public);
    }

    public function test_within_radius_scope_filters_using_place_coordinates(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'eventuser2@example.com',
            'password' => 'password',
        ]);

        $nearPlace = Place::create([
            'user_id' => $user->id,
            'name' => 'Near Place',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'latitude' => 40.0,
            'longitude' => -3.0,
        ]);

        $farPlace = Place::create([
            'user_id' => $user->id,
            'name' => 'Far Place',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'latitude' => 41.0,
            'longitude' => -3.0,
        ]);

        $nearEvent = Event::create([
            'title' => 'Near',
            'description' => 'desc',
            'date' => now()->toDateString(),
            'place_id' => $nearPlace->id,
            'user_id' => $user->id,
            'category' => 'social',
            'is_public' => true,
        ]);

        $farEvent = Event::create([
            'title' => 'Far',
            'description' => 'desc',
            'date' => now()->toDateString(),
            'place_id' => $farPlace->id,
            'user_id' => $user->id,
            'category' => 'social',
            'is_public' => true,
        ]);

        $events = Event::withinRadius(40.0, -3.0, 70)->get();

        $this->assertTrue($events->contains($nearEvent));
        $this->assertFalse($events->contains($farEvent));
    }
}
