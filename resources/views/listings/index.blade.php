@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Alojamientos</h1>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Tipo</label>
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="apartamento" {{ request('type')=='apartamento' ? 'selected' : '' }}>Apartamento</option>
                <option value="residencia" {{ request('type')=='residencia' ? 'selected' : '' }}>Residencia</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" value="{{ request('city') }}" class="form-control" placeholder="Ciudad" list="city-suggestions" autocomplete="off">
            <datalist id="city-suggestions"></datalist>
        </div>
        <div class="col-md-4">
            <label class="form-label">Precio (€)</label>
            <div id="price-slider"></div>
            <div class="d-flex justify-content-between small">
                <span id="price-slider-min"></span>
                <span id="price-slider-max"></span>
            </div>
            <input type="hidden" name="price_range" id="price_range" value="{{ request('price_range', '0,2000') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Radio de búsqueda (km)</label>
            <input type="range" class="form-range" min="1" max="50" name="radius" id="radius" value="{{ request('radius', 10) }}" oninput="radiusOutput.value = this.value">
            <div class="text-end small">
                <output id="radiusOutput">{{ request('radius', 10) }}</output> km
            </div>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <input type="hidden" name="latitude" id="latitude" value="{{ request('latitude') }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ request('longitude') }}">
    </form>

    <div id="listings-map" class="mb-4" style="height: 400px"
         data-listings='@json($mapListings)'
         data-center='@json(config('services.google_maps.default_center'))'
         data-zoom='{{ config('services.google_maps.default_zoom') }}'></div>

    <div id="listing-container" class="row">
        @include('listings.partials.cards', ['listings' => $listings])
    </div>
    <template id="skeleton-card-template">
        <div class="col-md-4 mb-4 skeleton-placeholder">
            <div class="card h-100 skeleton-card">
                <div class="skeleton-image"></div>
                <div class="card-body">
                    <div class="skeleton-text mb-2 w-75"></div>
                    <div class="skeleton-text w-50"></div>
                </div>
            </div>
        </div>
    </template>
    <div id="pagination-links" class="d-none">
        {{ $listings->appends(request()->query())->links() }}
    </div>
    <div id="load-more-sentinel"></div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initListingsMap" async defer></script>
@endsection
