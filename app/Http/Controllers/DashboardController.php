<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Listing;
use App\Models\Event;
use App\Services\RoomieMatchService;

class DashboardController extends Controller
{
    private RoomieMatchService $roomieMatchService;

    public function __construct(RoomieMatchService $roomieMatchService)
    {
        $this->middleware(['auth', 'verified']);
        $this->roomieMatchService = $roomieMatchService;
    }

    public function index(): View
    {
        $userId = auth()->id();

        $listingsCollection = Listing::byUser($userId)->latest()->get();
        $eventsCollection = Event::where('user_id', $userId)->orderByDesc('date')->get();
        $matchesCollection = $this->roomieMatchService->getMutualMatches($userId);

        return view('dashboard.index', [
            'listings' => $listingsCollection->take(3),
            'events' => $eventsCollection->take(3),
            'matches' => $matchesCollection->take(3),
            'listings_count' => $listingsCollection->count(),
            'events_count' => $eventsCollection->count(),
            'matches_count' => $matchesCollection->count(),
        ]);
    }
}
