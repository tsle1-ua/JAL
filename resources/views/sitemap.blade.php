<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($pages as $page)
    <url>
        <loc>{{ $page }}</loc>
    </url>
    @endforeach

    @foreach($listings as $listing)
    <url>
        <loc>{{ route('listings.show', $listing) }}</loc>
    </url>
    @endforeach

    @foreach($events as $event)
    <url>
        <loc>{{ route('events.show', $event) }}</loc>
    </url>
    @endforeach
</urlset>
