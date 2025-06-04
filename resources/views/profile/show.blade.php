@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Perfil de {{ auth()->user()->name }}</h1>

    <h3>Favoritos</h3>
    <div class="row">
        @forelse(auth()->user()->favoriteListings as $listing)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if($listing->first_image_url)
                        <img src="{{ $listing->first_image_url }}" class="card-img-top" alt="Imagen del piso">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $listing->title }}</h5>
                        <p class="card-text">{{ $listing->formatted_price }} - {{ $listing->city }}</p>
                        <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-primary">Ver</a>
                    </div>
                </div>
            </div>
        @empty
            <p>No tienes favoritos.</p>
        @endforelse
    </div>
</div>
@endsection
