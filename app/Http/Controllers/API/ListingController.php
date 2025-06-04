<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ListingService;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'city', 'type', 'min_price', 'max_price',
            'bedrooms', 'bathrooms', 'available_from', 'search'
        ]);

        if (!empty(array_filter($filters))) {
            $listings = $this->listingService->searchListingsPaginated($filters);
        } else {
            $listings = $this->listingService->getPaginatedListings();
        }

        return response()->json(ListingResource::collection($listings));
    }

    public function store(StoreListingRequest $request): JsonResponse
    {
        $images = $request->file('images', []);
        $listing = $this->listingService->createListing($request->validated(), $images);

        return response()->json(new ListingResource($listing), 201);
    }

    public function show(int $id): JsonResponse
    {
        $listing = $this->listingService->findListing($id);

        if (!$listing) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        return response()->json(new ListingResource($listing));
    }

    public function update(UpdateListingRequest $request, int $id): JsonResponse
    {
        $images = $request->file('images', []);
        $updated = $this->listingService->updateListing($id, $request->validated(), $images);

        if (!$updated) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        $listing = $this->listingService->findListing($id);

        return response()->json(new ListingResource($listing));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->listingService->deleteListing($id);

        if (!$deleted) {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        return response()->json(status: 204);
    }
}
