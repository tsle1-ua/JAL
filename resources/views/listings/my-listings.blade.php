@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mis Anuncios</h1>
    @if($listings->isEmpty())
        <p>Aún no has publicado ningún anuncio.</p>
    @else
        <div class="row">
            @foreach($listings as $listing)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($listing->first_image_url)
                            <img src="{{ $listing->first_image_url }}" class="card-img-top" alt="Imagen del alojamiento">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $listing->title }}</h5>
                            <p class="card-text">{{ $listing->city }} - {{ $listing->formatted_price }}</p>
                            <p class="card-text">Estado: <span class="{{ $listing->is_available ? 'text-success' : 'text-danger' }}">
                                {{ $listing->is_available ? 'Disponible' : 'Ocupado' }}</span></p>
                            <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                            <a href="{{ route('listings.edit', $listing) }}" class="btn btn-sm btn-secondary">Editar</a>
                            <form method="POST" action="{{ route('listings.toggle-availability', $listing) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-link">{{ $listing->is_available ? 'Marcar como no disponible' : 'Marcar como disponible' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
