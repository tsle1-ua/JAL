@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $event->title }}</h1>
    <p>{{ $event->description }}</p>
    <p><strong>Cuándo:</strong> {{ $event->formatted_date_time }}</p>
    <p><strong>Dónde:</strong> {{ $event->location }}</p>
    <p><strong>Precio:</strong> {{ $event->formatted_price }}</p>
    <p><strong>Aforo:</strong> {{ $event->current_attendees }} / {{ $event->max_attendees ?? '∞' }}</p>

    @if($event->place)
        <h4 class="mt-4">Ubicación</h4>
        <div class="ratio ratio-16x9">
            <iframe
                src="{{ $event->place->google_maps_embed_url }}"
                style="border:0;"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <p class="mt-2">
            <strong>{{ $event->place->name }}</strong><br>
            {{ $event->place->formatted_address }}<br>
            <a href="{{ $event->place->google_maps_link }}" target="_blank" rel="noopener">Ver en Google Maps</a>
        </p>
    @endif

    @auth
        @if(!$event->is_user_attending && $event->has_available_spots)
            <form method="POST" action="{{ route('events.register', $event) }}" class="d-inline">
                @csrf
                <button class="btn btn-success">Unirme</button>
            </form>
        @elseif($event->is_user_attending)
            <form method="POST" action="{{ route('events.unregister', $event) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">Cancelar asistencia</button>
            </form>
        @endif
    @endauth

    <h4 class="mt-4">Asistentes</h4>
    <ul>
        @forelse($event->attendees as $user)
            <li>{{ $user->name }}</li>
        @empty
            <li>Aún no hay asistentes.</li>
        @endforelse
    </ul>
</div>
@endsection
