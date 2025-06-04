@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Zonas de Ocio</h1>
    @auth
        <a href="{{ route('places.create') }}" class="btn btn-primary mb-3">Crear zona</a>
    @endauth

    @foreach($places as $place)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $place->name }}</h5>
                <p class="card-text">{{ $place->formatted_address }}</p>
            </div>
        </div>
    @endforeach
</div>
@endsection
