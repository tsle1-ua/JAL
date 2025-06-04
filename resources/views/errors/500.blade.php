@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4">Error del servidor</h1>
    <p class="lead">Ha ocurrido un problema inesperado.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Volver al inicio</a>
</div>
@endsection
