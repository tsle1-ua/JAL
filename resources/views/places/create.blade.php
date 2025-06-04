@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Zona de Ocio</h1>
    <form method="POST" action="{{ route('places.store') }}" role="form">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="place-name">Nombre</label>
            <input id="place-name" type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="place-description">Descripción</label>
            <textarea id="place-description" name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="place-address">Dirección</label>
            <input id="place-address" type="text" name="address" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="place-city">Ciudad</label>
            <input id="place-city" type="text" name="city" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
