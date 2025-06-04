@extends('layouts.app')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Bienvenido a Spandam</h1>
        <p class="lead">Tu plataforma integral para universitarios</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg mt-3">Regístrate</a>
    </div>
</div>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <i class="bi bi-house-fill display-5 text-primary"></i>
            <h3 class="mt-3">Alojamientos</h3>
            <p>Encuentra y publica pisos de forma sencilla y segura.</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="bi bi-people-fill display-5 text-primary"></i>
            <h3 class="mt-3">RoomieMatch</h3>
            <p>Conecta con compañeros de piso compatibles contigo.</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="bi bi-calendar-event-fill display-5 text-primary"></i>
            <h3 class="mt-3">Eventos</h3>
            <p>Descubre y organiza eventos universitarios cerca de ti.</p>
        </div>
    </div>
</div>

@if(isset($trendingEvents) && $trendingEvents->count())
<div class="bg-light py-5">
    <div class="container">
        <h2 class="mb-4 text-center">Eventos en Tendencia</h2>
        <div class="row g-4">
            @foreach($trendingEvents as $event)
                <div class="col-md-4">
                    <div class="card h-100">
                        @if($event->image_url)
                            <img src="{{ $event->image_url }}" class="card-img-top" alt="{{ $event->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text">{{ $event->short_date }} - {{ $event->location }}</p>
                            <p class="card-text"><small class="text-muted">{{ $event->current_attendees }} asistentes</small></p>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-primary">Ver más</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
