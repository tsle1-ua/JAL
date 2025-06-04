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
            'university', 'radius'
        ]);

        if (!empty(array_filter($filters))) {
            $listings = $this->listingService->searchListings($filters);
        } else {
            $listings = $this->listingService->getPaginatedListings();
        }

        $statistics = $this->listingService->getListingStatistics();

        return view('listings.index', compact('listings', 'statistics', 'filters'));
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
}