@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Evento</h1>
    <form method="POST" action="{{ route('events.update', $event) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" class="form-control" value="{{ $event->title }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" required>{{ $event->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="date" class="form-control" value="{{ $event->date->format('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Hora</label>
            <input type="time" name="time" class="form-control" value="{{ $event->time ? $event->time->format('H:i') : '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Lugar</label>
            <select name="place_id" class="form-select">
                <option value="">Selecciona una zona</option>
                @foreach($places as $place)
                    <option value="{{ $place->id }}" {{ $event->place_id == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Si no existe, <a href="{{ route('places.create') }}">crea una nueva zona</a>.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ $event->price }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Máximo de asistentes</label>
            <input type="number" name="max_attendees" class="form-control" value="{{ $event->max_attendees }}">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
