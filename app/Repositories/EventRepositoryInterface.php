<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    public function getAll(): Collection;
    
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    
    public function findById(int $id): ?Event;
    
    public function create(array $data): Event;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getUpcoming(): Collection;
    
    public function getByUser(int $userId): Collection;
    
    public function searchWithFilters(array $filters): Collection;

    public function searchWithFiltersPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;
    
    public function getNearby(float $latitude, float $longitude, float $radius): Collection;
}