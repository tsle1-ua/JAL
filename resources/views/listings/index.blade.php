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
            <input type="text" name="city" value="{{ request('city') }}" class="form-control" placeholder="Ciudad">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header">Buscar por ubicación</div>
        <div class="card-body">
            <div id="location-map" style="height: 300px;"></div>
            <div class="mt-3">
                <label for="radius" class="form-label">Radio: <span id="radius-value">10</span> km</label>
                <input type="range" id="radius" class="form-range" min="1" max="50" value="10">
            </div>
            <button id="search-location-btn" class="btn btn-primary mt-2">Buscar</button>
        </div>
    </div>

    <div id="location-results" class="row"></div>

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

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-sA+HgPmePi6Om4I/g30G6U9w1ygnA2SLEb24SgXQYJ0=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-o9N1j/cbmkuO0XdVW9nhXEBECqmWxaT21ztCuCso1xc=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('location-map').setView([40.4168, -3.7038], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker(map.getCenter(), { draggable: true }).addTo(map);
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
    });

    const radiusInput = document.getElementById('radius');
    const radiusValue = document.getElementById('radius-value');
    radiusInput.addEventListener('input', function(){
        radiusValue.textContent = radiusInput.value;
    });

    document.getElementById('search-location-btn').addEventListener('click', function(){
        fetch('{{ route('listings.search-location') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                latitude: marker.getLatLng().lat,
                longitude: marker.getLatLng().lng,
                radius: radiusInput.value
            })
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('location-results');
            container.innerHTML = '';
            if (data.success) {
                data.listings.forEach(listing => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.innerHTML = `<div class="card h-100">
                            <img src="${listing.image_url}" class="card-img-top" alt="Piso">
                            <div class="card-body">
                                <h5 class="card-title">${listing.title}</h5>
                                <p class="card-text">${listing.address}</p>
                                <p class="card-text">${listing.price}</p>
                                <a href="${listing.url}" class="btn btn-outline-primary btn-sm">Ver más</a>
                            </div>
                        </div>`;
                    container.appendChild(col);
                });
                if (data.listings.length === 0) {
                    container.innerHTML = '<p>No se encontraron alojamientos.</p>';
                }
            }
        });
    });
});
</script>
@endsection
