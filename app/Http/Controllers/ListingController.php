<?php

namespace App\Http\Controllers;

use App\Services\ListingService;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    protected $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'city', 'type', 'min_price', 'max_price',
            'bedrooms', 'bathrooms', 'available_from', 'search',
            'university', 'radius', 'latitude', 'longitude'
        ]);

        if (!isset($filters['radius'])) {
            $filters['radius'] = 10;
        }

        if ($request->filled('price_range')) {
            [$min, $max] = array_map('intval', explode(',', $request->input('price_range')));
            $filters['min_price'] = $min;
            $filters['max_price'] = $max;
        }

        if (!empty(array_filter($filters))) {
            $listings = $this->listingService->searchListingsPaginated($filters);
        } else {
            $listings = $this->listingService->getPaginatedListings();
        }

        $statistics = $this->listingService->getListingStatistics();

        return view('listings.index', compact('listings', 'statistics', 'filters'));
    }

    /**
     * Return listing cards as JSON for infinite scroll.
     */
    public function cards(Request $request): JsonResponse
    {
        $filters = $request->only([
            'city', 'type', 'min_price', 'max_price',
            'bedrooms', 'bathrooms', 'available_from', 'search',
            'university', 'radius', 'latitude', 'longitude'
        ]);

        if (!isset($filters['radius'])) {
            $filters['radius'] = 10;
        }

        if ($request->filled('price_range')) {
            [$min, $max] = array_map('intval', explode(',', $request->input('price_range')));
            $filters['min_price'] = $min;
            $filters['max_price'] = $max;
        }

        if (!empty(array_filter($filters))) {
            $listings = $this->listingService->searchListingsPaginated($filters);
        } else {
            $listings = $this->listingService->getPaginatedListings();
        }

        return response()->json([
            'html' => view('listings.partials.cards', ['listings' => $listings])->render(),
            'links' => $listings->appends($request->query())->links()->render(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('listings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreListingRequest $request): RedirectResponse
    {
        try {
            $images = $request->file('images', []);
            $listing = $this->listingService->createListing($request->validated(), $images);
            
            return redirect()->route('listings.show', $listing)
                           ->with('success', 'Anuncio creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Error al crear el anuncio: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $listing = $this->listingService->findListing($id);
        
        if (!$listing) {
            abort(404, 'Anuncio no encontrado.');
        }

        // Obtener anuncios relacionados (misma ciudad, tipo similar)
        $relatedListings = $this->listingService->searchListings([
            'city' => $listing->city,
            'type' => $listing->type,
        ])->where('id', '!=', $listing->id)->take(3);

        return view('listings.show', compact('listing', 'relatedListings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $listing = $this->listingService->findListing($id);
        
        if (!$listing) {
            abort(404, 'Anuncio no encontrado.');
        }

        // Verificar autorización
        if ($listing->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para editar este anuncio.');
        }

        return view('listings.edit', compact('listing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateListingRequest $request, int $id): RedirectResponse
    {
        try {
            $images = $request->file('images', []);
            $updated = $this->listingService->updateListing($id, $request->validated(), $images);
            
            if ($updated) {
                return redirect()->route('listings.show', $id)
                               ->with('success', 'Anuncio actualizado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo actualizar el anuncio.');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Error al actualizar el anuncio: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->listingService->deleteListing($id);
            
            if ($deleted) {
                return redirect()->route('listings.index')
                               ->with('success', 'Anuncio eliminado exitosamente.');
            }
            
            return back()->with('error', 'No se pudo eliminar el anuncio.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el anuncio: ' . $e->getMessage());
        }
    }

    /**
     * Display user's own listings.
     */
    public function myListings(): View
    {
        $listings = $this->listingService->getUserListings(auth()->id());
        
        return view('listings.my-listings', compact('listings'));
    }

    /**
     * Toggle listing availability.
     */
    public function toggleAvailability(int $id): RedirectResponse
    {
        try {
            $updated = $this->listingService->toggleAvailability($id);
            
            if ($updated) {
                return back()->with('success', 'Estado de disponibilidad actualizado.');
            }
            
            return back()->with('error', 'No se pudo actualizar la disponibilidad.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Search listings by location (AJAX).
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

        $listings = $this->listingService->getNearbyListings($latitude, $longitude, $radius);

        return response()->json([
            'success' => true,
            'listings' => $listings->map(function ($listing) {
                return [
                    'id' => $listing->id,
                    'title' => $listing->title,
                    'price' => $listing->formatted_price,
                    'address' => $listing->formatted_address,
                    'type' => $listing->type,
                    'bedrooms' => $listing->bedrooms,
                    'bathrooms' => $listing->bathrooms,
                    'image_url' => $listing->first_image_url,
                    'url' => route('listings.show', $listing),
                    'latitude' => $listing->latitude,
                    'longitude' => $listing->longitude,
                ];
            }),
        ]);
    }

    /**
     * Suggest city names for autocomplete.
     */
    public function citySuggestions(Request $request): JsonResponse
    {
        $term = $request->input('term');

        $cities = \App\Models\Listing::query()
            ->select('city')
            ->when($term, fn($q) => $q->where('city', 'like', "%{$term}%"))
            ->distinct()
            ->orderBy('city')
            ->limit(10)
            ->pluck('city');

        return response()->json($cities);
    }

    /**
     * Get listing statistics (AJAX).
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->listingService->getListingStatistics();

        return response()->json([
            'success' => true,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Schedule a visit for a listing.
     */
    public function scheduleVisit(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'visit_date' => 'required|date',
            'visit_time' => 'required'
        ]);

        // In a real app we would persist the visit in database or send notification
        $date = $request->visit_date;
        $time = $request->visit_time;

        return back()->with('success', "Visita reservada para {$date} a las {$time}.");
    }
}