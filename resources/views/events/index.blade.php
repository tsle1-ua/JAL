@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Eventos</h1>
    @auth
        <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Crear evento</a>
    @endauth

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
@endsection
