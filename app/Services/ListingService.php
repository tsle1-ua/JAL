<?php

namespace App\Services;

use App\Models\Listing;
use App\Repositories\ListingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ListingService
{
    protected $listingRepository;

    public function __construct(ListingRepositoryInterface $listingRepository)
    {
        $this->listingRepository = $listingRepository;
    }

    public function getAvailableListings(): Collection
    {
        return $this->listingRepository->getAvailable();
    }

    public function getPaginatedListings(int $perPage = 15)
    {
        return $this->listingRepository->getPaginated($perPage);
    }

    public function findListing(int $id): ?Listing
    {
        return $this->listingRepository->findById($id);
    }

    public function getUserListings(int $userId): Collection
    {
        return $this->listingRepository->getByUser($userId);
    }

    public function searchListings(array $filters): Collection
    {
        return $this->listingRepository->searchWithFilters($filters);
    }

    public function getNearbyListings(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->listingRepository->getNearby($latitude, $longitude, $radius);
    }

    public function createListing(array $data, array $images = []): Listing
    {
        return DB::transaction(function () use ($data, $images) {
            // Asignar usuario actual
            $data['user_id'] = auth()->id();

            // Procesar imágenes
            if (!empty($images)) {
                $data['image_paths'] = $this->uploadImages($images);
            }

            // Geocodificar dirección si no se proporcionan coordenadas
            if (!isset($data['latitude']) || !isset($data['longitude'])) {
                $coordinates = $this->geocodeAddress($data['address'] . ', ' . $data['city']);
                if ($coordinates) {
                    $data['latitude'] = $coordinates['lat'];
                    $data['longitude'] = $coordinates['lng'];
                }
            }

            return $this->listingRepository->create($data);
        });
    }

    public function updateListing(int $id, array $data, array $images = []): bool
    {
        return DB::transaction(function () use ($id, $data, $images) {
            $listing = $this->listingRepository->findById($id);
            
            if (!$listing) {
                return false;
            }

            // Verificar autorización
            if ($listing->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para editar este anuncio.');
            }

            // Procesar nuevas imágenes
            if (!empty($images)) {
                // Eliminar imágenes antiguas
                $this->deleteImages($listing->image_paths ?? []);
                
                // Subir nuevas imágenes
                $data['image_paths'] = $this->uploadImages($images);
            }

            // Actualizar coordenadas si cambió la dirección
            if (isset($data['address']) || isset($data['city'])) {
                $address = ($data['address'] ?? $listing->address) . ', ' . ($data['city'] ?? $listing->city);
                $coordinates = $this->geocodeAddress($address);
                if ($coordinates) {
                    $data['latitude'] = $coordinates['lat'];
                    $data['longitude'] = $coordinates['lng'];
                }
            }

            return $this->listingRepository->update($id, $data);
        });
    }

    public function deleteListing(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $listing = $this->listingRepository->findById($id);
            
            if (!$listing) {
                return false;
            }

            // Verificar autorización
            if ($listing->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para eliminar este anuncio.');
            }

            // Eliminar imágenes
            $this->deleteImages($listing->image_paths ?? []);

            return $this->listingRepository->delete($id);
        });
    }

    public function toggleAvailability(int $id): bool
    {
        $listing = $this->listingRepository->findById($id);
        
        if (!$listing) {
            return false;
        }

        // Verificar autorización
        if ($listing->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            throw new \Exception('No tienes permisos para modificar este anuncio.');
        }

        return $this->listingRepository->update($id, [
            'is_available' => !$listing->is_available
        ]);
    }

    protected function uploadImages(array $images): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('listings', 'public');
                $uploadedPaths[] = $path;
            }
        }

        return $uploadedPaths;
    }

    protected function deleteImages(array $imagePaths): void
    {
        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function geocodeAddress(string $address): ?array
    {
        // Implementación básica de geocodificación
        // En producción, usar servicio como Google Maps Geocoding API
        // Por ahora, devolvemos coordenadas de ejemplo
        
        // Esto es solo un ejemplo - en producción usar una API real
        $defaultCoordinates = [
            'Madrid' => ['lat' => 40.4168, 'lng' => -3.7038],
            'Barcelona' => ['lat' => 41.3851, 'lng' => 2.1734],
            'Valencia' => ['lat' => 39.4699, 'lng' => -0.3763],
            'Sevilla' => ['lat' => 37.3891, 'lng' => -5.9845],
        ];

        foreach ($defaultCoordinates as $city => $coords) {
            if (stripos($address, $city) !== false) {
                return $coords;
            }
        }

        return null;
    }

    public function getListingStatistics(): array
    {
        $total = $this->listingRepository->getAll()->count();
        $available = $this->listingRepository->getAvailable()->count();
        $avgPrice = $this->listingRepository->getAvailable()->avg('price');

        return [
            'total' => $total,
            'available' => $available,
            'occupied' => $total - $available,
            'average_price' => round($avgPrice, 2),
        ];
    }
}