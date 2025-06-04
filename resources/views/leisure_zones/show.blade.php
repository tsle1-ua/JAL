@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $zone->name }}</h1>
    @if($zone->image_url)
        <img src="{{ $zone->image_url }}" class="img-fluid mb-3" alt="{{ $zone->name }}">
    @endif
    <p><strong>Ciudad:</strong> {{ $zone->city }}</p>
    @if($zone->university)
        <p><strong>Universidad:</strong> {{ $zone->university }}</p>
    @endif
    <p>{{ $zone->description }}</p>
</div>
@endsection
