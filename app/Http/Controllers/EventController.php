<?php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'category', 'date', 'start_date', 'end_date', 
            'city', 'free', 'available_spots', 'search'
        ]);

        if (!empty(array_filter($filters))) {
            $events = $this->eventService->searchEventsPaginated($filters);
        } else {
            $events = $this->eventService->getPaginatedEvents();
        }

        $statistics = $this->eventService->getEventStatistics();
        $upcomingEvents = $this->eventService->getUpcomingEvents()->take(5);

        return view('events.index', compact('events', 'statistics', 'upcomingEvents', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $places = Place::all();
        return view('events.create', compact('places'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        try {
            $image = $request->file('image');
            $event = $this->eventService->createEvent($request->validated(), $image);
            
            return redirect()->route('events.show', $event)
                           ->with('success', 'Evento creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Error al crear el evento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $event = $this->eventService->findEvent($id);
        
        if (!$event) {
            abort(404, 'Evento no encontrado.');
        }

        // Obtener eventos relacionados (misma categoría o fecha similar)
        $relatedEvents = $this->eventService->searchEvents([
            'category' => $event->category_id,
        ])->where('id', '!=', $event->id)->take(3);

        return view('events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $event = $this->eventService->findEvent($id);

        if (!$event) {
            abort(404, 'Evento no encontrado.');
        }

        // Verificar autorización
        if ($event->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para editar este evento.');
        }

        $places = Place::all();

        return view('events.edit', compact('event', 'places'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, int $id): RedirectResponse
    {
        try {
            $image = $request->file('image');
            $updated = $this->eventService->updateEvent($id, $request->validated(), $image);
            
            if ($updated) {
                return redirect()->route('events.show', $id)
                               ->with('success', 'Evento actualizado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo actualizar el evento.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Error al actualizar el evento: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->eventService->deleteEvent($id);
            
            if ($deleted) {
                return redirect()->route('events.index')
                               ->with('success', 'Evento eliminado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo eliminar el evento.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el evento: ' . $e->getMessage());
        }
    }

    /**
     * Display user's own events.
     */
    public function myEvents(): View
    {
        $events = $this->eventService->getUserEvents(auth()->id());
        
        return view('events.my-events', compact('events'));
    }

    /**
     * Toggle event visibility.
     */
    public function toggleVisibility(int $id): RedirectResponse
    {
        try {
            $updated = $this->eventService->toggleEventVisibility($id);
            
            if ($updated) {
                return back()->with('success', 'Visibilidad del evento actualizada.');
            }
            
            return back()->with('error', 'No se pudo actualizar la visibilidad.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Register attendance to an event.
     */
    public function registerAttendance(int $id): RedirectResponse
    {
        try {
            $registered = $this->eventService->registerAttendance($id);
            
            if ($registered) {
                return back()->with('success', 'Te has registrado al evento exitosamente.');
            }
            
            return back()->with('error', 'No se pudo registrar tu asistencia.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Unregister attendance from an event.
     */
    public function unregisterAttendance(int $id): RedirectResponse
    {
        try {
            $unregistered = $this->eventService->unregisterAttendance($id);
            
            if ($unregistered) {
                return back()->with('success', 'Te has dado de baja del evento.');
            }
            
            return back()->with('error', 'No se pudo cancelar tu registro.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Search events by location (AJAX).
     */
    public function searchByLocation(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10;

        $events = $this->eventService->getNearbyEvents($latitude, $longitude, $radius);

        return response()->json([
            'success' => true,
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->formatted_date_time,
                    'category' => $event->category?->name,
                    'price' => $event->formatted_price,
                    'location' => $event->location,
                    'image_url' => $event->image_url,
                    'url' => route('events.show', $event),
                    'latitude' => $event->place?->latitude,
                    'longitude' => $event->place?->longitude,
                ];
            }),
        ]);
    }

    /**
     * Get event statistics (AJAX).
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->eventService->getEventStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get recommended events for user (AJAX).
     */
    public function recommended(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $recommendedEvents = $this->eventService->getRecommendedEvents(auth()->id());
        
        return response()->json([
            'success' => true,
            'events' => $recommendedEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->formatted_date_time,
                    'category' => $event->category?->name,
                    'location' => $event->location,
                    'url' => route('events.show', $event),
                ];
            }),
        ]);
    }

    /**
     * Return public upcoming events as JSON for API clients.
     */
    public function apiIndex(): JsonResponse
    {
        $events = $this->eventService->getUpcomingEvents();

        return response()->json(['events' => $events]);
    }
}