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
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div id="listing-container" class="row">
        @include('listings.partials.cards', ['listings' => $listings])
    </div>
    <div id="pagination-links" class="d-none">
        {{ $listings->appends(request()->query())->links() }}
    </div>
    <div id="load-more-sentinel"></div>
</div>
@endsection
