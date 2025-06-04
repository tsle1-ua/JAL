@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4">Página no encontrada</h1>
    <p class="lead">La página que buscas no existe.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Volver al inicio</a>
</div>
@endsection
