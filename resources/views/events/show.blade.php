@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $event->title }}</h1>
    <p>{{ $event->description }}</p>
    <p><strong>Cuándo:</strong> {{ $event->formatted_date_time }}</p>
    <p><strong>Dónde:</strong> {{ $event->location }}</p>
    <p><strong>Precio:</strong> {{ $event->formatted_price }}</p>
    <p><strong>Aforo:</strong> {{ $event->current_attendees }} / {{ $event->max_attendees ?? '∞' }}</p>

    @auth
        @if(!$event->is_user_attending && $event->has_available_spots)
            <form method="POST" action="{{ route('events.register', $event) }}" class="d-inline" role="form">
                @csrf
                <button class="btn btn-success">Unirme</button>
            </form>
        @elseif($event->is_user_attending)
            <form method="POST" action="{{ route('events.unregister', $event) }}" class="d-inline" role="form">
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
