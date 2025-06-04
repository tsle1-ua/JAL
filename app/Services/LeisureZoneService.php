<?php

namespace App\Services;

use App\Models\LeisureZone;
use App\Repositories\LeisureZoneRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LeisureZoneService
{
    protected $zoneRepository;

    public function __construct(LeisureZoneRepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    public function getPromotedZones(): Collection
    {
        return $this->zoneRepository->getPromoted();
    }

    public function getPaginatedZones(int $perPage = 15)
    {
        return $this->zoneRepository->getPaginated($perPage);
    }

    public function findZone(int $id): ?LeisureZone
    {
        return $this->zoneRepository->findById($id);
    }

    public function searchZones(array $filters): Collection
    {
        return $this->zoneRepository->searchWithFilters($filters);
    }

    public function createZone(array $data, ?UploadedFile $image = null): LeisureZone
    {
        return DB::transaction(function () use ($data, $image) {
            $data['user_id'] = auth()->id();

            if ($image) {
                $data['image_path'] = $this->uploadImage($image);
            }

            return $this->zoneRepository->create($data);
        });
    }

    public function updateZone(int $id, array $data, ?UploadedFile $image = null): bool
    {
        return DB::transaction(function () use ($id, $data, $image) {
            $zone = $this->zoneRepository->findById($id);

            if (!$zone) {
                return false;
            }

            if ($zone->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para editar esta zona de ocio.');
            }

            if ($image) {
                if ($zone->image_path) {
                    $this->deleteImage($zone->image_path);
                }
                $data['image_path'] = $this->uploadImage($image);
            }

            return $this->zoneRepository->update($id, $data);
        });
    }

    public function deleteZone(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $zone = $this->zoneRepository->findById($id);

            if (!$zone) {
                return false;
            }

            if ($zone->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para eliminar esta zona de ocio.');
            }

            if ($zone->image_path) {
                $this->deleteImage($zone->image_path);
            }

            return $this->zoneRepository->delete($id);
        });
    }

    protected function uploadImage(UploadedFile $image): string
    {
        return $image->store('leisure_zones', 'public');
    }

    protected function deleteImage(string $imagePath): void
    {
        Storage::disk('public')->delete($imagePath);
    }
}
