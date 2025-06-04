<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'categories', 'category', 'date', 'start_date', 'end_date',
            'city', 'free', 'available_spots', 'search'
        ]);

        if (!empty(array_filter($filters))) {
            $events = $this->eventService->searchEventsPaginated($filters);
        } else {
            $events = $this->eventService->getPaginatedEvents();
        }

        return response()->json(EventResource::collection($events));
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $image = $request->file('image');
        $event = $this->eventService->createEvent($request->validated(), $image);

        return response()->json(new EventResource($event), 201);
    }

    public function show(int $id): JsonResponse
    {
        $event = $this->eventService->findEvent($id);

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json(new EventResource($event));
    }

    public function update(UpdateEventRequest $request, int $id): JsonResponse
    {
        $image = $request->file('image');
        $updated = $this->eventService->updateEvent($id, $request->validated(), $image);

        if (!$updated) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event = $this->eventService->findEvent($id);

        return response()->json(new EventResource($event));
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->eventService->deleteEvent($id);

        if (!$deleted) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json(status: 204);
    }
}
