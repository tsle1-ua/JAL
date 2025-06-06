@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            @if($listing->first_image_url)
                <img src="{{ $listing->first_image_url }}" class="img-fluid mb-3" alt="Imagen principal">
            @endif
            <h2>{{ $listing->title }}</h2>
            <p>{{ $listing->formatted_address }}</p>
            <p>{{ $listing->formatted_price }} - {{ $listing->square_meters ?? '?' }} m²</p>
            <p>Baños: {{ $listing->bathrooms }} | Habitaciones: {{ $listing->bedrooms }}</p>
            <p>Ocupantes: {!! $listing->occupant_icons !!} ({{ $listing->current_occupants }}/{{ $listing->max_occupants ?? 'N/A' }})</p>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Reservar visita</h5>
                    <form method="POST" action="{{ route('listings.schedule', $listing) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="visit_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hora</label>
                            <input type="time" name="visit_time" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Reservar</button>
                    </form>
                    @if($listing->phone)
                        <div class="mt-3">
                            <a href="tel:{{ $listing->phone }}" class="btn btn-outline-secondary w-100 mb-2">Llamar</a>
                            <a href="https://wa.me/{{ $listing->phone }}" target="_blank" class="btn btn-success w-100">WhatsApp</a>
                        </div>
                    @endif
                    @auth
                        @php
                            $isFavorited = auth()->user()->favoriteListings->contains($listing->id);
                        @endphp
                        <div class="mt-3">
                            @if($isFavorited)
                                <form method="POST" action="{{ route('favorites.destroy', $listing) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger w-100">Quitar de favoritos</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('favorites.store', $listing) }}">
                                    @csrf
                                    <button class="btn btn-outline-danger w-100">Añadir a favoritos</button>
                                </form>
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
