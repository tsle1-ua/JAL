@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Alojamientos</h1>
    <form method="GET" class="row g-3 mb-4 position-relative">
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="apartamento" {{ request('type')=='apartamento' ? 'selected' : '' }}>Apartamento</option>
                <option value="residencia" {{ request('type')=='residencia' ? 'selected' : '' }}>Residencia</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" value="{{ request('city') }}" class="form-control" placeholder="Ciudad">
        </div>
        <div class="col-md-3 position-relative">
            <label class="form-label">Buscar</label>
            <input type="text" name="search" id="listing-search" value="{{ request('search') }}" class="form-control" placeholder="Buscar...">
            <div id="listing-suggestions" class="list-group position-absolute w-100 d-none"></div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="row">
        @foreach($listings as $listing)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($listing->first_image_url)
                        <img src="{{ $listing->first_image_url }}" class="card-img-top" alt="Imagen del piso">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $listing->title }}</h5>
                        <p class="card-text">{{ $listing->city }} - {{ $listing->formatted_price }}</p>
                        <p class="card-text">
                            {!! $listing->occupant_icons !!}
                        </p>
                        <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-primary">Ver más</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
