@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Zona</h1>
    <form method="POST" action="{{ route('places.update', $place) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ $place->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control">{{ $place->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="address" class="form-control" value="{{ $place->address }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" class="form-control" value="{{ $place->city }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
