<?php

namespace App\Repositories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentListingRepository implements ListingRepositoryInterface
{
    public function getAll(): Collection
    {
        return Listing::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Listing::with('user')
                     ->available()
                     ->orderBy('created_at', 'desc')
                     ->paginate($perPage);
    }

    public function findById(int $id): ?Listing
    {
        return Listing::with('user')->find($id);
    }

    public function create(array $data): Listing
    {
        return Listing::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $listing = Listing::find($id);
        return $listing ? $listing->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $listing = Listing::find($id);
        return $listing ? $listing->delete() : false;
    }

    public function getAvailable(): Collection
    {
        return Listing::with('user')->available()->orderBy('created_at', 'desc')->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Listing::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    public function searchWithFilters(array $filters): Collection
    {
        $query = Listing::with('user')->available();

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->inCity($filters['city']);
        });

        $query->when(isset($filters['type']), function ($q) use ($filters) {
            $q->ofType($filters['type']);
        });

        $query->when(isset($filters['min_price']), function ($q) use ($filters) {
            $q->where('price', '>=', $filters['min_price']);
        });

        $query->when(isset($filters['max_price']), function ($q) use ($filters) {
            $q->where('price', '<=', $filters['max_price']);
        });

        $query->when(isset($filters['bedrooms']), function ($q) use ($filters) {
            $q->minBedrooms($filters['bedrooms']);
        });

        $query->when(isset($filters['bathrooms']), function ($q) use ($filters) {
            $q->minBathrooms($filters['bathrooms']);
        });

        $query->when(isset($filters['available_from']), function ($q) use ($filters) {
            $q->availableFrom($filters['available_from']);
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->search($filters['search']);
        });

        $query->when(isset($filters['latitude']) && isset($filters['longitude']), function ($q) use ($filters) {
            $radius = $filters['radius'] ?? 5;
            $q->withinRadius($filters['latitude'], $filters['longitude'], $radius);
        });

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function searchWithFiltersPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Listing::with('user')->available();

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->inCity($filters['city']);
        });

        $query->when(isset($filters['type']), function ($q) use ($filters) {
            $q->ofType($filters['type']);
        });

        $query->when(isset($filters['min_price']), function ($q) use ($filters) {
            $q->where('price', '>=', $filters['min_price']);
        });

        $query->when(isset($filters['max_price']), function ($q) use ($filters) {
            $q->where('price', '<=', $filters['max_price']);
        });

        $query->when(isset($filters['bedrooms']), function ($q) use ($filters) {
            $q->minBedrooms($filters['bedrooms']);
        });

        $query->when(isset($filters['bathrooms']), function ($q) use ($filters) {
            $q->minBathrooms($filters['bathrooms']);
        });

        $query->when(isset($filters['available_from']), function ($q) use ($filters) {
            $q->availableFrom($filters['available_from']);
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->search($filters['search']);
        });

        $query->when(isset($filters['latitude']) && isset($filters['longitude']), function ($q) use ($filters) {
            $radius = $filters['radius'] ?? 5;
            $q->withinRadius($filters['latitude'], $filters['longitude'], $radius);
        });

        return $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
    }

    public function getNearby(float $latitude, float $longitude, float $radius): Collection
    {
        return Listing::with('user')
                     ->available()
                     ->withinRadius($latitude, $longitude, $radius)
                     ->get();
    }
}