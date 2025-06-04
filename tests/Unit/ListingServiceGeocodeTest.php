<?php

namespace Tests\Unit;

use App\Services\ListingService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ListingServiceGeocodeTest extends TestCase
{
    /**
     * Creates a minimal subclass exposing the geocodeAddress method.
     */
    private function service(): ListingService
    {
        return new class extends ListingService {
            public function __construct()
            {
                // Skip parent constructor
            }

            public function geocode(string $address)
            {
                return $this->geocodeAddress($address);
            }
        };
    }

    public function test_geocode_address_returns_coordinates(): void
    {
        Http::fake([
            'https://maps.googleapis.com/maps/api/geocode/json*' => Http::response([
                'results' => [
                    [
                        'geometry' => [
                            'location' => ['lat' => 1.23, 'lng' => 4.56],
                        ],
                    ],
                ],
                'status' => 'OK',
            ], 200),
        ]);

        $result = $this->service()->geocode('Test Address');

        $this->assertSame(['lat' => 1.23, 'lng' => 4.56], $result);
    }

    public function test_geocode_address_returns_null_on_failure(): void
    {
        Http::fake([
            'https://maps.googleapis.com/maps/api/geocode/json*' => Http::response([], 500),
        ]);

        $result = $this->service()->geocode('Bad Address');

        $this->assertNull($result);
    }
}

