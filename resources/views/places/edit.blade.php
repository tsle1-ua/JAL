@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Zona</h1>
    <form method="POST" action="{{ route('places.update', $place) }}" role="form">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label" for="edit-place-name">Nombre</label>
            <input id="edit-place-name" type="text" name="name" class="form-control" value="{{ $place->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="edit-place-description">Descripción</label>
            <textarea id="edit-place-description" name="description" class="form-control">{{ $place->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="edit-place-address">Dirección</label>
            <input id="edit-place-address" type="text" name="address" class="form-control" value="{{ $place->address }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="edit-place-city">Ciudad</label>
            <input id="edit-place-city" type="text" name="city" class="form-control" value="{{ $place->city }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
