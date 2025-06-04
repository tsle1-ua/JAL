@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Zonas de ocio</h1>
    <form method="GET" class="row g-2 mb-4" role="search">
        <div class="col-md-4">
            <label for="filter-city" class="visually-hidden">Ciudad</label>
            <input id="filter-city" type="text" name="city" class="form-control" placeholder="Ciudad" value="{{ request('city') }}">
        </div>
        <div class="col-md-4">
            <label for="filter-university" class="visually-hidden">Universidad</label>
            <input id="filter-university" type="text" name="university" class="form-control" placeholder="Universidad" value="{{ request('university') }}">
        </div>
        <div class="col-md-4">
            <label for="filter-search" class="visually-hidden">Buscar</label>
            <input id="filter-search" type="text" name="search" class="form-control" placeholder="Buscar" value="{{ request('search') }}">
        </div>
    </form>
    <div class="row">
        @forelse($zones as $zone)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    @if($zone->image_url)
                        <img src="{{ $zone->image_url }}" class="card-img-top" alt="{{ $zone->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $zone->name }}</h5>
                        <p class="card-text">{{ $zone->city }} - {{ $zone->university }}</p>
                        <a href="{{ route('leisure-zones.show', $zone) }}" class="btn btn-primary">Ver</a>
                    </div>
                </div>
            </div>
        @empty
            <p>No se encontraron zonas de ocio.</p>
        @endforelse
    </div>
</div>
@endsection
