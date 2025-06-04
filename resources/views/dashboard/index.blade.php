@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Panel de control</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Mis anuncios</h5>
                    <p class="display-6">{{ $listings_count }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Mis eventos</h5>
                    <p class="display-6">{{ $events_count }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Matches Roomie</h5>
                    <p class="display-6">{{ $matches_count }}</p>
                </div>
            </div>
        </div>
    </div>

    <h3>Anuncios recientes</h3>
    @forelse($listings as $listing)
        <div class="mb-2">
            <a href="{{ route('listings.show', $listing) }}">{{ $listing->title }}</a>
            <small class="text-muted">{{ $listing->city }} - {{ $listing->formatted_price }}</small>
        </div>
    @empty
        <p>No has publicado anuncios.</p>
    @endforelse

    <hr>
    <h3>Próximos eventos</h3>
    @forelse($events as $event)
        <div class="mb-2">
            <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
            <small class="text-muted">{{ $event->short_date }}</small>
        </div>
    @empty
        <p>No has creado eventos.</p>
    @endforelse

    <hr>
    <h3>Últimos matches</h3>
    @forelse($matches as $match)
        <div class="mb-2">
            <a href="{{ route('roomie.conversation', $match->match_id) }}">{{ $match->name }}</a>
            <small class="text-muted">{{ $match->matched_at->diffForHumans() }}</small>
        </div>
    @empty
        <p>No tienes matches todavía.</p>
    @endforelse
</div>
@endsection
