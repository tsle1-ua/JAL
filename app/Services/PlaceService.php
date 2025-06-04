<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;

class PlaceService
{
    public function getAllPlaces(): Collection
    {
        return Place::all();
    }

    public function findPlace(int $id): ?Place
    {
        return Place::find($id);
    }

    public function searchPlaces(array $filters): Collection
    {
        $query = Place::query();

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->inCity($filters['city']);
        });

        $query->when(isset($filters['category']), function ($q) use ($filters) {
            $q->byCategory($filters['category']);
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->search($filters['search']);
        });

        return $query->get();
    }
}
