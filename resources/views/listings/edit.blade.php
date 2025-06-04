@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Anuncio</h1>
    <form method="POST" action="{{ route('listings.update', $listing) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" value="{{ old('title', $listing->title) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $listing->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="address" value="{{ old('address', $listing->address) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" value="{{ old('city', $listing->city) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Código Postal</label>
            <input type="text" name="zip_code" value="{{ old('zip_code', $listing->zip_code) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Precio mensual (€)</label>
            <input type="number" step="0.01" name="price" value="{{ old('price', $listing->price) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de propiedad</label>
            <select name="type" class="form-select" required>
                <option value="apartamento" {{ old('type', $listing->type)=='apartamento' ? 'selected' : '' }}>Apartamento</option>
                <option value="residencia" {{ old('type', $listing->type)=='residencia' ? 'selected' : '' }}>Residencia</option>
                <option value="habitacion" {{ old('type', $listing->type)=='habitacion' ? 'selected' : '' }}>Habitación</option>
                <option value="casa" {{ old('type', $listing->type)=='casa' ? 'selected' : '' }}>Casa</option>
                <option value="estudio" {{ old('type', $listing->type)=='estudio' ? 'selected' : '' }}>Estudio</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Metros cuadrados</label>
            <input type="number" name="square_meters" value="{{ old('square_meters', $listing->square_meters) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Ocupantes actuales</label>
            <input type="number" name="current_occupants" value="{{ old('current_occupants', $listing->current_occupants) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Aforo máximo</label>
            <input type="number" name="max_occupants" value="{{ old('max_occupants', $listing->max_occupants) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Teléfono de contacto</label>
            <input type="text" name="phone" value="{{ old('phone', $listing->phone) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Habitaciones</label>
            <input type="number" name="bedrooms" value="{{ old('bedrooms', $listing->bedrooms) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Baños</label>
            <input type="number" step="0.5" name="bathrooms" value="{{ old('bathrooms', $listing->bathrooms) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Disponible desde</label>
            <input type="date" name="available_from" value="{{ old('available_from', $listing->available_from->toDateString()) }}" class="form-control" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_available" class="form-check-input" id="is_available" value="1" {{ old('is_available', $listing->is_available) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_available">Disponible</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Latitud</label>
            <input type="number" step="0.000001" name="latitude" value="{{ old('latitude', $listing->latitude) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Longitud</label>
            <input type="number" step="0.000001" name="longitude" value="{{ old('longitude', $listing->longitude) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Imágenes</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
