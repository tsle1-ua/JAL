<?php

namespace App\Repositories;

use App\Models\Listing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListingRepositoryInterface
{
    public function getAll(): Collection;
    
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    
    public function findById(int $id): ?Listing;
    
    public function create(array $data): Listing;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getAvailable(): Collection;
    
    public function getByUser(int $userId): Collection;
    
    public function searchWithFilters(array $filters): Collection;
    
    public function getNearby(float $latitude, float $longitude, float $radius): Collection;
}