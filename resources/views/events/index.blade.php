@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.17/index.global.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.17/index.global.min.css">
<div class="container">
    <h1 class="mb-4">Eventos</h1>
    @auth
        <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Crear evento</a>
    @endauth

    @auth
        @if(isset($recommendedEvents) && $recommendedEvents->isNotEmpty())
            <div class="mb-4">
                <h2 class="mb-3 text-primary">Recomendados para ti</h2>
                @foreach($recommendedEvents as $event)
                    <div class="card mb-2 border-primary">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text">
                                <small class="text-muted">{{ $event->formatted_date_time }} - {{ $event->location }}</small>
                            </p>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-primary">Ver</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endauth

    @php
        $calendarEvents = $events->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->date->format('Y-m-d') . ($event->time ? 'T' . $event->time->format('H:i:s') : ''),
                'url' => route('events.show', $event),
            ];
        });

        $mapEvents = $events->map(function ($event) {
            return [
                'title' => $event->title,
                'url' => route('events.show', $event),
                'lat' => optional($event->place)->latitude,
                'lng' => optional($event->place)->longitude,
            ];
        });
    @endphp

    <div class="mb-3">
        <button id="toggle-view" class="btn btn-secondary">Ver calendario</button>
    </div>
    <div id="event-map" class="mb-4" style="height: 400px" data-events='@json($mapEvents)'
         data-center='@json(config('services.google_maps.default_center'))'
         data-zoom='{{ config('services.google_maps.default_zoom') }}'></div>
    <div id="event-list">
    @foreach($events as $event)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $event->title }}</h5>
                <p class="card-text">{{ $event->description }}</p>
                <p class="card-text"><small class="text-muted">{{ $event->formatted_date_time }} - {{ $event->location }}</small></p>
                <p class="card-text">Precio: {{ $event->formatted_price }}</p>
                <p class="card-text">Aforo: {{ $event->current_attendees }} / {{ $event->max_attendees ?? '∞' }}</p>
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Ver</a>
            </div>
        </div>
    @endforeach
    {{ $events->appends(request()->query())->links() }}
    </div>

    <div id="calendar" class="d-none" data-events='@json($calendarEvents)'></div>

    @isset($places)
        <hr>
        <h2 class="mb-3">Zonas de Ocio</h2>
        @foreach($places as $place)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $place->name }}</h5>
                    <p class="card-text">{{ $place->formatted_address }}</p>
                </div>
            </div>
        @endforeach
@endisset
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initEventMap" async defer></script>
@endsection
