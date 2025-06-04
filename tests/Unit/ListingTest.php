<?php

namespace Tests\Unit;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_scope_returns_only_available_listings(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        Listing::create([
            'user_id' => $user->id,
            'title' => 'Available',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'price' => 100,
            'type' => 'casa',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'available_from' => now(),
            'is_available' => true,
        ]);

        Listing::create([
            'user_id' => $user->id,
            'title' => 'Not Available',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'price' => 100,
            'type' => 'casa',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'available_from' => now(),
            'is_available' => false,
        ]);

        $available = Listing::available()->get();

        $this->assertCount(1, $available);
        $this->assertTrue($available->first()->is_available);
    }

    public function test_within_radius_scope_filters_by_coordinates(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user2@example.com',
            'password' => 'password',
        ]);

        $near = Listing::create([
            'user_id' => $user->id,
            'title' => 'Near',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'price' => 100,
            'type' => 'casa',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'available_from' => now(),
            'is_available' => true,
            'latitude' => 40.0,
            'longitude' => -3.0,
        ]);

        $far = Listing::create([
            'user_id' => $user->id,
            'title' => 'Far',
            'description' => 'desc',
            'address' => 'addr',
            'city' => 'City',
            'price' => 100,
            'type' => 'casa',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'available_from' => now(),
            'is_available' => true,
            'latitude' => 41.0,
            'longitude' => -3.0,
        ]);

        $results = Listing::withinRadius(40.0, -3.0, 70)->get();

        $this->assertTrue($results->contains($near));
        $this->assertFalse($results->contains($far));
    }
}
