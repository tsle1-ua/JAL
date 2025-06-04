<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EventService
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getUpcomingEvents(): Collection
    {
        return $this->eventRepository->getUpcoming();
    }

    public function getPaginatedEvents(int $perPage = 15)
    {
        return $this->eventRepository->getPaginated($perPage);
    }

    public function findEvent(int $id): ?Event
    {
        return $this->eventRepository->findById($id);
    }

    public function getUserEvents(int $userId): Collection
    {
        return $this->eventRepository->getByUser($userId);
    }

    public function searchEvents(array $filters): Collection
    {
        return $this->eventRepository->searchWithFilters($filters);
    }

    public function getNearbyEvents(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->eventRepository->getNearby($latitude, $longitude, $radius);
    }

    public function createEvent(array $data, ?UploadedFile $image = null): Event
    {
        return DB::transaction(function () use ($data, $image) {
            // Asignar usuario actual
            $data['user_id'] = auth()->id();

            // Procesar imagen
            if ($image) {
                $data['image_path'] = $this->uploadImage($image);
            }

            // Validar y procesar fechas
            $data = $this->processEventDates($data);

            return $this->eventRepository->create($data);
        });
    }

    public function updateEvent(int $id, array $data, ?UploadedFile $image = null): bool
    {
        return DB::transaction(function () use ($id, $data, $image) {
            $event = $this->eventRepository->findById($id);
            
            if (!$event) {
                return false;
            }

            // Verificar autorización
            if ($event->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para editar este evento.');
            }

            // Procesar nueva imagen
            if ($image) {
                // Eliminar imagen antigua
                if ($event->image_path) {
                    $this->deleteImage($event->image_path);
                }
                
                // Subir nueva imagen
                $data['image_path'] = $this->uploadImage($image);
            }

            // Validar y procesar fechas
            $data = $this->processEventDates($data);

            return $this->eventRepository->update($id, $data);
        });
    }

    public function deleteEvent(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $event = $this->eventRepository->findById($id);
            
            if (!$event) {
                return false;
            }

            // Verificar autorización
            if ($event->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                throw new \Exception('No tienes permisos para eliminar este evento.');
            }

            // Eliminar imagen
            if ($event->image_path) {
                $this->deleteImage($event->image_path);
            }

            return $this->eventRepository->delete($id);
        });
    }

    public function toggleEventVisibility(int $id): bool
    {
        $event = $this->eventRepository->findById($id);
        
        if (!$event) {
            return false;
        }

        // Verificar autorización
        if ($event->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            throw new \Exception('No tienes permisos para modificar este evento.');
        }

        return $this->eventRepository->update($id, [
            'is_public' => !$event->is_public
        ]);
    }

    public function registerAttendance(int $eventId): bool
    {
        $event = $this->eventRepository->findById($eventId);

        if (!$event) {
            return false;
        }

        // Verificar si hay cupo disponible
        if (!$event->has_available_spots) {
            throw new \Exception('No hay cupos disponibles para este evento.');
        }

        if ($event->attendees()->where('user_id', auth()->id())->exists()) {
            throw new \Exception('Ya estás registrado en este evento.');
        }

        $event->attendees()->attach(auth()->id());

        return $this->eventRepository->update($eventId, [
            'current_attendees' => $event->current_attendees + 1
        ]);
    }

    public function unregisterAttendance(int $eventId): bool
    {
        $event = $this->eventRepository->findById($eventId);
        
        if (!$event) {
            return false;
        }

        if (!$event->attendees()->where('user_id', auth()->id())->exists()) {
            return false;
        }

        $event->attendees()->detach(auth()->id());

        return $this->eventRepository->update($eventId, [
            'current_attendees' => max(0, $event->current_attendees - 1)
        ]);
    }

    protected function uploadImage(UploadedFile $image): string
    {
        return $image->store('events', 's3');
    }

    protected function deleteImage(string $imagePath): void
    {
        Storage::disk('s3')->delete($imagePath);
    }

    protected function processEventDates(array $data): array
    {
        // Si se proporciona fecha y hora, crear end_datetime automáticamente
        if (isset($data['date']) && isset($data['time']) && !isset($data['end_datetime'])) {
            $startDateTime = \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i', 
                $data['date'] . ' ' . $data['time']
            );
            
            // Por defecto, eventos duran 2 horas
            $data['end_datetime'] = $startDateTime->addHours(2);
        }

        return $data;
    }

    public function getEventsByCategory(): array
    {
        $events = $this->eventRepository->getUpcoming();
        
        return $events->groupBy('category')->map(function ($categoryEvents) {
            return $categoryEvents->count();
        })->toArray();
    }

    public function getEventStatistics(): array
    {
        $allEvents = $this->eventRepository->getAll();
        $upcomingEvents = $this->eventRepository->getUpcoming();
        
        return [
            'total' => $allEvents->count(),
            'upcoming' => $upcomingEvents->count(),
            'past' => $allEvents->count() - $upcomingEvents->count(),
            'by_category' => $this->getEventsByCategory(),
            'this_week' => $upcomingEvents->filter(function ($event) {
                return $event->date->isCurrentWeek();
            })->count(),
        ];
    }

    public function getRecommendedEvents(int $userId, int $limit = 5): Collection
    {
        // Obtener eventos recomendados basados en el perfil del usuario
        $user = auth()->user();
        $profile = $user->profile;
        
        $query = $this->eventRepository->getUpcoming();
        
        // Si el usuario tiene universidad, priorizar eventos en esa ciudad
        if ($profile && $profile->university_name) {
            // Esto es una implementación simplificada
            // En producción, se podría usar un algoritmo más sofisticado
            return $query->take($limit);
        }
        
        return $query->take($limit);
    }
}