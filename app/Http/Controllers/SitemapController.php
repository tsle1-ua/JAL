<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Event;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Display the sitemap.
     */
    public function index(): Response
    {
        $listings = Listing::available()->get();
        $events = Event::public()->get();

        $pages = [
            route('home'),
            route('help'),
            route('privacy'),
            route('terms'),
            route('listings.index'),
            route('events.index'),
            route('ocio'),
        ];

        return response()->view('sitemap', [
            'listings' => $listings,
            'events' => $events,
            'pages' => $pages,
        ], 200)->header('Content-Type', 'application/xml');
    }
}
