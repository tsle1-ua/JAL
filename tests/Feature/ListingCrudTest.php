<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Listing;
use App\Repositories\ListingRepositoryInterface;
use App\Repositories\EloquentListingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->bind(ListingRepositoryInterface::class, EloquentListingRepository::class);
        $this->withoutVite();
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => 'owner',
        ]);
    }

    private function listingData(): array
    {
        return [
            'title' => 'Test Listing',
            'description' => 'Test Description',
            'address' => '123 Street',
            'city' => 'Madrid',
            'price' => 1000,
            'type' => 'apartamento',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'available_from' => now()->addDay()->format('Y-m-d'),
        ];
    }

    public function test_authenticated_user_can_create_listing(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->post('/listings', $this->listingData());

        $listing = Listing::first();

        $response->assertRedirect(route('listings.show', $listing));
        $this->assertDatabaseHas('listings', [
            'title' => 'Test Listing',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_view_listing(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $listing = Listing::create(array_merge($this->listingData(), [
            'user_id' => $user->id,
        ]));

        $response = $this->get('/listings/' . $listing->id);

        $response->assertOk();
        $response->assertSee('Test Listing');
    }

    public function test_authenticated_user_can_edit_listing(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $listing = Listing::create(array_merge($this->listingData(), [
            'user_id' => $user->id,
        ]));

        $updated = $this->listingData();
        $updated['title'] = 'Updated Title';

        $response = $this->put('/listings/' . $listing->id, $updated);

        $response->assertRedirect(route('listings.show', $listing->id));
        $this->assertDatabaseHas('listings', [
            'id' => $listing->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_authenticated_user_can_delete_listing(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $listing = Listing::create(array_merge($this->listingData(), [
            'user_id' => $user->id,
        ]));

        $response = $this->delete('/listings/' . $listing->id);

        $response->assertRedirect('/listings');
        $this->assertDatabaseMissing('listings', [
            'id' => $listing->id,
        ]);
    }
}
