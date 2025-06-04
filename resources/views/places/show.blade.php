@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $place->name }}</h1>
    <p>{{ $place->description }}</p>
    <p><strong>Dirección:</strong> {{ $place->formatted_address }}</p>
</div>
@endsection
