<?php

namespace App\Repositories;

use App\Models\LeisureZone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeisureZoneRepositoryInterface
{
    public function getAll(): Collection;

    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?LeisureZone;

    public function create(array $data): LeisureZone;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getPromoted(): Collection;

    public function searchWithFilters(array $filters): Collection;
}
