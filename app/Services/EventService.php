<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Repositories\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewEventInFollowedCategoryNotification;

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

    public function searchEventsPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->eventRepository->searchWithFiltersPaginated($filters, $perPage);
    }

    public function getNearbyEvents(float $latitude, float $longitude, float $radius = 10): Collection
    {
        return $this->eventRepository->getNearby($latitude, $longitude, $radius);
    }

    public function createEvent(array $data, ?UploadedFile $image = null): Event
    {
        $event = DB::transaction(function () use ($data, $image) {
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

        Cache::forget('event.statistics');

        $this->notifyFollowers($event);

        return $event;
    }

    public function updateEvent(int $id, array $data, ?UploadedFile $image = null): bool
    {
        $updated = DB::transaction(function () use ($id, $data, $image) {
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

        if ($updated) {
            Cache::forget('event.statistics');
        }

        return $updated;
    }

    public function deleteEvent(int $id): bool
    {
        $deleted = DB::transaction(function () use ($id) {
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

        if ($deleted) {
            Cache::forget('event.statistics');
        }

        return $deleted;
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
        return DB::transaction(function () use ($eventId) {
            $event = Event::where('id', $eventId)->lockForUpdate()->first();

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
            $event->increment('current_attendees');

            return true;
        });
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
        return $image->store('events', 'public');
    }

    protected function deleteImage(string $imagePath): void
    {
        Storage::disk('public')->delete($imagePath);
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
        $events = $this->eventRepository->getUpcoming()->load('category');

        return $events->groupBy(fn ($e) => $e->category?->name)
            ->map(fn ($categoryEvents) => $categoryEvents->count())
            ->toArray();
    }

    public function getEventStatistics(): array
    {
        return Cache::remember('event.statistics', 300, function () {
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
        });
    }

    public function getRecommendedEvents(int $userId, int $limit = 5): Collection
    {
        // Obtener el usuario con sus relaciones necesarias
        $user = User::with(['profile', 'attendingEvents', 'events'])->find($userId);
        if (!$user) {
            return collect();
        }

        // Categorías en las que el usuario ha mostrado interés
        $preferredCategories = $user->attendingEvents->pluck('category_id')
            ->merge($user->events->pluck('category_id'))
            ->filter()
            ->unique();

        $query = Event::with(['user', 'place'])
            ->public()
            ->upcoming();

        if ($preferredCategories->isNotEmpty()) {
            $query->whereIn('category_id', $preferredCategories);
        }

        // Si el usuario tiene universidad, priorizar eventos en esa ciudad
        if ($user->profile && $user->profile->university_name) {
            $city = $user->profile->university_name;
            $query->whereHas('place', function ($q) use ($city) {
                $q->where('city', 'like', '%' . $city . '%');
            });
        }

        return $query->orderBy('date')->take($limit)->get();
    }

    protected function notifyFollowers(Event $event): void
    {
        if (!$event->category) {
            return;
        }

        $followers = $event->category->followers()->whereNotNull('fcm_token')->get();

        Notification::send(
            $followers,
            new NewEventInFollowedCategoryNotification($event)
        );
    }
}