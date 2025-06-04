<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\ListingService;
use App\Services\EventService;
use App\Services\PlaceService;

Route::get('/listings', function (Request $request, ListingService $service) {
    $filters = $request->only([
        'city', 'type', 'min_price', 'max_price',
        'bedrooms', 'bathrooms', 'available_from',
        'search', 'university', 'radius'
    ]);

    $listings = !empty(array_filter($filters))
        ? $service->searchListings($filters)
        : $service->getPaginatedListings();

    return response()->json($listings);
});

Route::get('/listings/{id}', function (int $id, ListingService $service) {
    $listing = $service->findListing($id);

    return $listing
        ? response()->json($listing)
        : response()->json(['message' => 'Listing not found'], 404);
});

Route::get('/events', function (Request $request, EventService $service) {
    $filters = $request->only([
        'category', 'date', 'start_date', 'end_date',
        'city', 'free', 'available_spots', 'search'
    ]);

    $events = !empty(array_filter($filters))
        ? $service->searchEvents($filters)
        : $service->getPaginatedEvents();

    return response()->json($events);
});

Route::get('/events/{id}', function (int $id, EventService $service) {
    $event = $service->findEvent($id);

    return $event
        ? response()->json($event)
        : response()->json(['message' => 'Event not found'], 404);
});

Route::get('/places', function (Request $request, PlaceService $service) {
    $filters = $request->only(['city', 'category', 'search']);
    $places = !empty(array_filter($filters))
        ? $service->searchPlaces($filters)
        : $service->getAllPlaces();

    return response()->json($places);
});

Route::get('/places/{id}', function (int $id, PlaceService $service) {
    $place = $service->findPlace($id);

    return $place
        ? response()->json($place)
        : response()->json(['message' => 'Place not found'], 404);
});
