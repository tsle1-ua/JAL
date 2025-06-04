<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function store(Listing $listing): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->favoriteListings()->where('listing_id', $listing->id)->exists()) {
            $user->favoriteListings()->attach($listing->id);
        }

        return back()->with('success', 'Anuncio añadido a favoritos.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $user = auth()->user();
        $user->favoriteListings()->detach($listing->id);

        return back()->with('success', 'Anuncio eliminado de favoritos.');
    }
}

