<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function getAll(): Collection
    {
        return Event::with(['user', 'place'])->orderBy('date')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Event::with(['user', 'place'])
                   ->public()
                   ->upcoming()
                   ->paginate($perPage);
    }

    public function findById(int $id): ?Event
    {
        return Event::with(['user', 'place', 'attendees'])->find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $event = Event::find($id);
        return $event ? $event->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $event = Event::find($id);
        return $event ? $event->delete() : false;
    }

    public function getUpcoming(): Collection
    {
        return Event::with(['user', 'place'])
                   ->public()
                   ->upcoming()
                   ->limit(10)
                   ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Event::with('place')
                   ->where('user_id', $userId)
                   ->orderBy('date')
                   ->get();
    }

    public function searchWithFilters(array $filters): Collection
    {
        $query = Event::with(['user', 'place'])->public();

        $query->when(isset($filters['category']), function ($q) use ($filters) {
            $q->byCategory($filters['category']);
        });

        $query->when(isset($filters['date']), function ($q) use ($filters) {
            $q->onDate($filters['date']);
        });

        $query->when(isset($filters['start_date']) && isset($filters['end_date']), function ($q) use ($filters) {
            $q->betweenDates($filters['start_date'], $filters['end_date']);
        });

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->inCity($filters['city']);
        });

        $query->when(isset($filters['free']) && $filters['free'], function ($q) {
            $q->free();
        });

        $query->when(isset($filters['available_spots']) && $filters['available_spots'], function ($q) {
            $q->withAvailableSpots();
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->search($filters['search']);
        });

        return $query->upcoming()->get();
    }

    public function searchWithFiltersPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Event::with(['user', 'place'])->public();

        $query->when(isset($filters['category']), function ($q) use ($filters) {
            $q->byCategory($filters['category']);
        });

        $query->when(isset($filters['date']), function ($q) use ($filters) {
            $q->onDate($filters['date']);
        });

        $query->when(isset($filters['start_date']) && isset($filters['end_date']), function ($q) use ($filters) {
            $q->betweenDates($filters['start_date'], $filters['end_date']);
        });

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->inCity($filters['city']);
        });

        $query->when(isset($filters['free']) && $filters['free'], function ($q) {
            $q->free();
        });

        $query->when(isset($filters['available_spots']) && $filters['available_spots'], function ($q) {
            $q->withAvailableSpots();
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->search($filters['search']);
        });

        return $query->upcoming()->paginate($perPage)->withQueryString();
    }

    public function getNearby(float $latitude, float $longitude, float $radius): Collection
    {
        return Event::with(['user', 'place'])
                   ->public()
                   ->upcoming()
                   ->withinRadius($latitude, $longitude, $radius)
                   ->get();
    }
}