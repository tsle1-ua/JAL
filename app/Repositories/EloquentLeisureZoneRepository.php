<?php

namespace App\Repositories;

use App\Models\LeisureZone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentLeisureZoneRepository implements LeisureZoneRepositoryInterface
{
    public function getAll(): Collection
    {
        return LeisureZone::with('user')->orderByDesc('is_promoted')->orderBy('name')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return LeisureZone::with('user')
            ->orderByDesc('is_promoted')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): ?LeisureZone
    {
        return LeisureZone::with('user')->find($id);
    }

    public function create(array $data): LeisureZone
    {
        return LeisureZone::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $zone = LeisureZone::find($id);
        return $zone ? $zone->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $zone = LeisureZone::find($id);
        return $zone ? $zone->delete() : false;
    }

    public function getPromoted(): Collection
    {
        return LeisureZone::with('user')->where('is_promoted', true)->orderBy('name')->get();
    }

    public function searchWithFilters(array $filters): Collection
    {
        $query = LeisureZone::with('user');

        $query->when(isset($filters['city']), function ($q) use ($filters) {
            $q->where('city', 'like', '%' . $filters['city'] . '%');
        });

        $query->when(isset($filters['university']), function ($q) use ($filters) {
            $q->where('university', 'like', '%' . $filters['university'] . '%');
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->where('name', 'like', '%' . $filters['search'] . '%')
              ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        });

        return $query->orderByDesc('is_promoted')->orderBy('name')->get();
    }
}
