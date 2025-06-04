@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Crear Anuncio</h1>
    <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="address" value="{{ old('address') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" value="{{ old('city') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Código Postal</label>
            <input type="text" name="zip_code" value="{{ old('zip_code') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Precio mensual (€)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de propiedad</label>
            <select name="type" class="form-select" required>
                <option value="apartamento" {{ old('type')=='apartamento' ? 'selected' : '' }}>Apartamento</option>
                <option value="residencia" {{ old('type')=='residencia' ? 'selected' : '' }}>Residencia</option>
                <option value="habitacion" {{ old('type')=='habitacion' ? 'selected' : '' }}>Habitación</option>
                <option value="casa" {{ old('type')=='casa' ? 'selected' : '' }}>Casa</option>
                <option value="estudio" {{ old('type')=='estudio' ? 'selected' : '' }}>Estudio</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Metros cuadrados</label>
            <input type="number" name="square_meters" value="{{ old('square_meters') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Ocupantes actuales</label>
            <input type="number" name="current_occupants" value="{{ old('current_occupants') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Aforo máximo</label>
            <input type="number" name="max_occupants" value="{{ old('max_occupants') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Teléfono de contacto</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Habitaciones</label>
            <input type="number" name="bedrooms" value="{{ old('bedrooms') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Baños</label>
            <input type="number" step="0.5" name="bathrooms" value="{{ old('bathrooms') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Disponible desde</label>
            <input type="date" name="available_from" value="{{ old('available_from') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Ubicación en el mapa</label>
            <div id="listing-map" style="height: 300px"
                 data-center='@json(config('services.google_maps.default_center'))'
                 data-zoom='{{ config('services.google_maps.default_zoom') }}'></div>
            <div class="form-text">Haz clic en el mapa para colocar el marcador.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Latitud</label>
            <input type="number" step="0.000001" name="latitude" value="{{ old('latitude') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Longitud</label>
            <input type="number" step="0.000001" name="longitude" value="{{ old('longitude') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Imágenes</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initListingMap" async defer></script>
@endsection
