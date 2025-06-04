@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Evento</h1>
    <form method="POST" action="{{ route('events.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Hora</label>
            <input type="time" name="time" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Lugar</label>
            <select name="place_id" class="form-select">
                <option value="">Selecciona una zona</option>
                @foreach($places as $place)
                    <option value="{{ $place->id }}">{{ $place->name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Si no existe, <a href="{{ route('places.create') }}">crea una nueva zona</a>.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" name="price" class="form-control" value="0">
        </div>
        <div class="mb-3">
            <label class="form-label">Máximo de asistentes</label>
            <input type="number" name="max_attendees" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
