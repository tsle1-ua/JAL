@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis Eventos</h1>
    <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">Crear evento</a>

    @forelse($events as $event)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $event->title }}</h5>
                <p class="card-text">{{ $event->description }}</p>
                <p class="card-text"><small class="text-muted">{{ $event->formatted_date_time }} - {{ $event->location }}</small></p>
                <p class="card-text">Precio: {{ $event->formatted_price }}</p>
                <p class="card-text">Aforo: {{ $event->current_attendees }} / {{ $event->max_attendees ?? '∞' }}</p>
                <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este evento?')">Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <p>No has creado ningún evento aún.</p>
    @endforelse
</div>
@endsection
